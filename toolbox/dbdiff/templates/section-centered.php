<div class="section centered">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2 style="text-align: center;">
                <?php if(isset($section_title)) echo $section_title; ?>
                </h2>
            </div>
            <div class="section-content">
                <?php $this->renderViews('section-content'); ?>
            </div>
        </div>
    </div>
</div>
