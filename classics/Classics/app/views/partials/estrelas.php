<?php
// Exibe 5 estrelas preenchidas conforme $notaEstrelas (1 a 5)
$notaEstrelas = max(0, min(5, (int) ($notaEstrelas ?? 0)));
?>
<span class="cl-estrelas" role="img" aria-label="<?= $notaEstrelas ?> de 5 estrelas">
    <?php for ($estrela = 1; $estrela <= 5; $estrela++): ?>
        <i class="bi <?= $estrela <= $notaEstrelas ? 'bi-star-fill' : 'bi-star' ?>"></i>
    <?php endfor; ?>
</span>
