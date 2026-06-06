<?php if (!empty($_SESSION["flash"])): ?>
    <?php
    $type = $_SESSION["flash"]["type"] === "success" ? "success" : "danger";
    ?>

    <div class="alert alert-<?= $type ?> alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION["flash"]["message"], ENT_QUOTES, "UTF-8") ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>

    <?php unset($_SESSION["flash"]); ?>
<?php endif; ?>