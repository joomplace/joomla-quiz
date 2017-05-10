<div style="position: relative;" id="qcontainer<?= $displayData->get('c_id') ?>">
    <div class="jq_question_text_cont">
        <div id="quest_div<?= $displayData->get('c_id') ?>" class="jq_question_inner" style="position: relative;">
            <?php /* ?>
            <div class="progress progress-striped active">
                <div class="bar" style="width: 14%;">
                    <div class="jq_question_info_container" id="jq_question_info_container"> <span id="jq_quest_num_container">Question 2 of 14</span>
                        <span id="jq_points_container">Points for the correct answer <?= number_format($displayData->get('points',0),'1'); ?></span>
                    </div>
                </div>
            </div>
            <?php */ ?>
            <span class="error_messagebox_quest" id="error_messagebox_quest<?= $displayData->get('c_id') ?>"></span>
            <?= $displayData->get('markup') ?>
        </div>
    </div>
</div>