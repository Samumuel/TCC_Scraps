<?php 

namespace app\services;

use app\models\Empresa;
use app\repositories\AgendamentoRepository;
use app\repositories\EmpresaRepository;
use app\repositories\UsuarioRepository;
use DateTime;

class EmpresaService {

    // RF02/RN02: situações do cadastro da empresa
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADA = 'aprovada';
    public const STATUS_SUSPENSA = 'suspensa';
    public const STATUS_REJEITADA = 'rejeitada';
    public const STATUS_VALIDOS = [self::STATUS_PENDENTE, self::STATUS_APROVADA, self::STATUS_SUSPENSA, self::STATUS_REJEITADA];

    // Ações do painel do administrador: status de origem permitidos => novo status
    private const ACOES_STATUS = [
        'aprovar' => [[self::STATUS_PENDENTE, self::STATUS_REJEITADA], self::STATUS_APROVADA],
        'rejeitar' => [[self::STATUS_PENDENTE], self::STATUS_REJEITADA],
        'suspender' => [[self::STATUS_APROVADA], self::STATUS_SUSPENSA],
        'reativar' => [[self::STATUS_SUSPENSA], self::STATUS_APROVADA]
    ];

    private EmpresaRepository $repository;
    private UsuarioRepository $usuarioRepository;

    public function __construct(){

        $this->repository = new EmpresaRepository();
        $this->usuarioRepository = new UsuarioRepository();

    }

    public function getEmpresa(int $id){
        return $this->repository->getEmpresa($id);
    }

    // $notificarAdmin = false quando é o próprio administrador que cadastra a empresa
    public function saveEmpresa(Empresa $empresa, bool $notificarAdmin = true){
        $emailNormalizado = $this->normalizarEmail((string) $empresa->getEmail());
        $empresa->setEmail($emailNormalizado);

        if ($this->repository->hasEmail($emailNormalizado) || $this->usuarioRepository->hasEmail($emailNormalizado)) {
            return false;
        }

        if (!$this->repository->saveEmpresa($empresa)) {
            return false;
        }

        // O administrador é avisado de que há um cadastro aguardando aprovação
        if ($notificarAdmin) {
            (new NotificacaoService())->notificarNovaEmpresa((string) $empresa->getNome());
        }

        return true;
    }

    // Exclusão feita pelo administrador: apaga a empresa com seus dados e avisa os clientes com reservas futuras
    public function deleteEmpresa(int $id): bool {
        $agora = new DateTime();
        $reservasAtivas = (new AgendamentoRepository())->getReservasAtivasDaEmpresa($id, $agora->format('Y-m-d'), $agora->format('H:i:s'));

        if (!$this->repository->deleteEmpresaComDados($id)) {
            return false;
        }

        (new NotificacaoService())->notificarEmpresaExcluida($reservasAtivas);

        return true;
    }

    public function updateEmpresa(Empresa $empresa) {
        $empresaAtual = $this->repository->getEmpresa($empresa->getIdEmpresa());
        if (!$empresaAtual) {
            return false;
        }

        // A própria empresa não altera o status; só o administrador (RF02)
        $empresa->setStatus($empresaAtual['Status'] ?? self::STATUS_PENDENTE);

        return $this->salvarAlteracoes($empresa, $empresaAtual);
    }

    // Edição feita pelo administrador, que também pode mudar o status
    public function updateEmpresaPeloAdmin(Empresa $empresa): bool
    {
        $empresaAtual = $this->repository->getEmpresa($empresa->getIdEmpresa());
        if (!$empresaAtual) {
            return false;
        }

        $statusAnterior = $this->normalizarStatus((string) ($empresaAtual['Status'] ?? ''));
        $novoStatus = $this->normalizarStatus((string) $empresa->getStatus());
        if (!in_array($novoStatus, self::STATUS_VALIDOS, true)) {
            $novoStatus = $statusAnterior;
        }
        $empresa->setStatus($novoStatus);

        if (!$this->salvarAlteracoes($empresa, $empresaAtual)) {
            return false;
        }

        if ($novoStatus !== $statusAnterior) {
            (new NotificacaoService())->notificarStatusEmpresa($empresa->getIdEmpresa(), $statusAnterior, $novoStatus);
        }

        return true;
    }

    // Aprovar, rejeitar, suspender ou reativar pelo painel do administrador
    public function alterarStatus(int $empresaId, string $acao): array
    {
        if (!isset(self::ACOES_STATUS[$acao])) {
            return ['sucesso' => false, 'erro' => 'acao'];
        }

        $statusAtual = $this->repository->getStatus($empresaId);
        if ($statusAtual === null) {
            return ['sucesso' => false, 'erro' => 'empresa'];
        }

        [$statusPermitidos, $novoStatus] = self::ACOES_STATUS[$acao];
        if (!in_array($statusAtual, $statusPermitidos, true)) {
            return ['sucesso' => false, 'erro' => 'status'];
        }

        if (!$this->repository->atualizarStatus($empresaId, $novoStatus)) {
            return ['sucesso' => false, 'erro' => 'falha'];
        }

        (new NotificacaoService())->notificarStatusEmpresa($empresaId, $statusAtual, $novoStatus);

        return ['sucesso' => true, 'status' => $novoStatus];
    }

    public function getEmpresasPorStatus(string $status): array
    {
        $status = $this->normalizarStatus($status);

        return in_array($status, self::STATUS_VALIDOS, true) ? $this->repository->getEmpresasPorStatus($status) : [];
    }

    // Quantidade de empresas em cada status válido
    public function contarPorStatus(): array
    {
        $contagem = $this->repository->contarPorStatus();
        $resultado = [];
        foreach (self::STATUS_VALIDOS as $status) {
            $resultado[$status] = (int) ($contagem[$status] ?? 0);
        }

        return $resultado;
    }

    // RN02: lido do banco a cada acesso, para valer assim que o administrador mudar o status
    public function isAprovada(int $empresaId): bool
    {
        return $empresaId > 0 && $this->repository->getStatus($empresaId) === self::STATUS_APROVADA;
    }

    public function getStatus(int $empresaId): ?string
    {
        return $empresaId > 0 ? $this->repository->getStatus($empresaId) : null;
    }

    private function salvarAlteracoes(Empresa $empresa, array $empresaAtual): bool
    {
        $emailNormalizado = $this->normalizarEmail((string) $empresa->getEmail());
        $empresa->setEmail($emailNormalizado);

        if (
            $this->repository->hasEmail($emailNormalizado, $empresa->getIdEmpresa()) ||
            $this->usuarioRepository->hasEmail($emailNormalizado)
        ) {
            return false;
        }

        // Senha em branco mantém a atual; senha nova é gravada criptografada (RNF04)
        $novaSenha = (string) $empresa->getSenha();
        $empresa->setSenha($novaSenha === '' ? (string) ($empresaAtual['Senha'] ?? '') : password_hash($novaSenha, PASSWORD_BCRYPT));

        return $this->repository->updateEmpresa($empresa);
    }

    private function normalizarStatus(string $status): string
    {
        return strtolower(trim($status));
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
