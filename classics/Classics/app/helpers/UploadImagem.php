<?php

namespace app\helpers;

// Upload de imagens (fotos de atividades e de perfil), usado pelos controllers
class UploadImagem
{
    private const TAMANHO_MAXIMO = 5_242_880; // 5 MB
    private const EXTENSOES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    // Salva a imagem em public/uploads/{pasta}. Sem arquivo enviado, devolve sucesso com foto null.
    public static function salvar(?array $arquivo, string $pasta, string $prefixoNome): array
    {
        if ($arquivo === null || !isset($arquivo['error']) || (int) $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
            return self::resultado(true);
        }

        if ((int) $arquivo['error'] !== UPLOAD_ERR_OK) {
            return self::resultado(false, null, 'Falha no upload da imagem.');
        }

        if ((int) ($arquivo['size'] ?? 0) > self::TAMANHO_MAXIMO) {
            return self::resultado(false, null, 'A imagem deve ter no máximo 5MB.');
        }

        $tmp = (string) ($arquivo['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return self::resultado(false, null, 'Arquivo de imagem inválido.');
        }

        // Valida o tipo real do arquivo, e não só a extensão informada
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return self::resultado(false, null, 'Não foi possível validar o arquivo enviado.');
        }
        $mime = finfo_file($finfo, $tmp);
        finfo_close($finfo);

        if (!isset(self::EXTENSOES[$mime])) {
            return self::resultado(false, null, 'Formato de imagem inválido. Use JPG, PNG ou WEBP.');
        }

        if (getimagesize($tmp) === false) {
            return self::resultado(false, null, 'O arquivo enviado não é uma imagem válida.');
        }

        try {
            $identificador = bin2hex(random_bytes(8));
        } catch (\Exception $e) {
            return self::resultado(false, null, 'Não foi possível gerar o nome da imagem.');
        }

        $nomeArquivo = $prefixoNome . '_' . $identificador . '.' . self::EXTENSOES[$mime];
        $diretorio = self::diretorio($pasta);

        if (!is_dir($diretorio) && !mkdir($diretorio, 0775, true) && !is_dir($diretorio)) {
            return self::resultado(false, null, 'Não foi possível preparar o diretório de upload.');
        }

        if (!move_uploaded_file($tmp, $diretorio . DIRECTORY_SEPARATOR . $nomeArquivo)) {
            return self::resultado(false, null, 'Não foi possível salvar a imagem enviada.');
        }

        return self::resultado(true, 'uploads/' . $pasta . '/' . $nomeArquivo);
    }

    // Remove uma imagem salva por este helper (somente dentro de public/uploads/{pasta})
    public static function remover(string $caminhoRelativo, string $pasta): void
    {
        $prefixo = 'uploads/' . $pasta . '/';
        if ($caminhoRelativo === '' || strpos($caminhoRelativo, $prefixo) !== 0 || str_contains($caminhoRelativo, '..')) {
            return;
        }

        $caminhoCompleto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $caminhoRelativo);
        if (is_file($caminhoCompleto)) {
            unlink($caminhoCompleto);
        }
    }

    private static function diretorio(string $pasta): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $pasta;
    }

    private static function resultado(bool $sucesso, ?string $foto = null, ?string $erro = null): array
    {
        return ['sucesso' => $sucesso, 'foto' => $foto, 'erro' => $erro];
    }
}
