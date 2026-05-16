<?php if (isset($paginator) && $paginator->totalPages() > 1): ?>
    <?php $currentParams = $_GET; ?>
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
            <?php
            $currentParams['p'] = max(1, $paginator->currentPage() - 1);
            $prevLink = 'index.php?' . http_build_query($currentParams);
            ?>
            <li class="page-item <?= $paginator->hasPrev() ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $paginator->hasPrev() ? e($prevLink) : '#' ?>">Previous</a>
            </li>

            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                <?php
                $currentParams['p'] = $i;
                $link = 'index.php?' . http_build_query($currentParams);
                ?>
                <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                    <a class="page-link" href="<?= e($link) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php
            $currentParams['p'] = min($paginator->totalPages(), $paginator->currentPage() + 1);
            $nextLink = 'index.php?' . http_build_query($currentParams);
            ?>
            <li class="page-item <?= $paginator->hasNext() ? '' : 'disabled' ?>">
                <a class="page-link" href="<?= $paginator->hasNext() ? e($nextLink) : '#' ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
