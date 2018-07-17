<?php
/** @var \DrdPlus\RulesSkeleton\RulesController $controller */
if ($controller->getWebVersions()->isCurrentVersionStable()) {
    return;
} ?>
<div class="warning message">Od této testovací verze změny nečekej, ty se dějí hlavně v
  <a href="https://pph.drdplus.info/?version=master">Pravidlech pro hráče</a>.
</div>
