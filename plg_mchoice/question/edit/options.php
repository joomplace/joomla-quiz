<?php
/**
 * Created by PhpStorm.
 * User: Alexandr
 * Date: 10.04.2017
 * Time: 15:08
 */

JFactory::getDocument()->addScript('https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js');
JFactory::getDocument()->addScript('https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular-sanitize.min.js');
JFactory::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/angular-ui-tinymce/0.0.18/tinymce.js');
///https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-sanitize.min.js

$editor = JEditor::getInstance('tinymce');
$buttons = $editor->getButtons('tinymce');
JFactory::getDocument()->setBase(JUri::root());

$i = 0;
$scripts = array_map(function($btn)use(&$i){
    if(!$btn->options){
        return false;
    }
    ob_start();
    ?>
    !(function(){
    var getBtnOptions = new Function("return <?= $btn->options ?>"),
    btnOptions = getBtnOptions(),
    modalWidth = btnOptions.size && btnOptions.size.x ?  btnOptions.size.x : null,
    modalHeight = btnOptions.size && btnOptions.size.y ?  btnOptions.size.y : null;
    editor.addButton("button-<?= $i++ ?><?= $btn->text ?>", {
    text: "<?= $btn->text ?>",
    title: "<?= $btn->text ?>",
    icon: "none icon-<?= $btn->name ?>",
    onclick: function () {
    var modalOptions = {
    title  : "<?= $btn->text ?>",
    url : '<?= JUri::root().JRoute::_($btn->link) ?>',
    buttons: [{
    text   : "Close",
    onclick: "close"
    }]
    }
    if(modalWidth){
    modalOptions.width = modalWidth;
    }
    if(modalHeight){
    modalOptions.height = modalHeight;
    }
    editor.windowManager.open(modalOptions);
    }
    });
    })();
    <?php
    $buf = ob_get_contents();
    ob_end_clean();
    return $buf;
},$buttons);
$scripts = array_filter($scripts);

$data = $displayData;
$db = JFactory::getDbo();

$quests_data = JLayoutHelper::render('question.json.subquestions', $data->get('item')->c_id, JPATH_SITE.'/plugins/joomlaquiz/mchoice/');
?>
<style>
    .closing-sp{
        display: none;
    }
    .active .closing-sp{
        padding: 5px 0px 5px 10px;
        margin: 0px 0px 0px 10px;
        border-left: 1px solid #ddd;
        display: inline;
    }
    @media (min-width: 768px){
        .tabloid{
            display: table;
        }
        .tabloid > *[class*="span"]{
            display: table-cell;
            float: none;
            height: 100%;
            border-left: 1px solid #ddd;
            padding: 0px 15px;
        }
        .tabloid > *[class*="span"]:first-child{
            border-left: 0px solid #ddd;
            padding-left: 0px;
        }
        .tabloid > *[class*="span"]:last-child{
            padding-right: 0px;
        }
    }
    .nav ng-transclude > li > a {
        display: block;
        text-decoration: none;
    }
    .nav-tabs ng-transclude > li {
        float: left;
    }
    .nav-tabs ng-transclude > li > a {
        padding-right: 12px;
        padding-left: 12px;
        margin-right: 2px;
        line-height: 14px;
    }
    .nav-tabs ng-transclude > li > a {
        padding-top: 8px;
        padding-bottom: 8px;
        line-height: 18px;
        border: 1px solid transparent;
        -webkit-border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        border-radius: 4px 4px 0 0;
    }
    .nav-tabs ng-transclude > li > a:hover,
    .nav-tabs ng-transclude > li > a:focus {
        border-color: #eee #eee #ddd;
    }
    .nav-tabs ng-transclude > .active > a,
    .nav-tabs ng-transclude > .active > a:hover,
    .nav-tabs ng-transclude > .active > a:focus {
        color: #555;
        background-color: #fff;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
        cursor: default;
    }
</style>
<script>
    jQuery(document).ready(function($){
        var app = angular.module('question-edit', ['ngSanitize','ui.tinymce'])
            .controller('questionEditCtrl', ['$scope','$rootScope', function($scope, $rootScope) {
                $rootScope.questions = $scope.questions = <?= $quests_data ?>;

                $scope.tinymceOptions = {
                    setup: function(editor) {
                        <?= implode("\n",$scripts); ?>
                    },
                    onChange: function(e) {
                        // put logic here for keypress and cut/paste changes
                    },
                    inline: false,
                    plugins : "table link code hr charmap autolink lists importcss jdragdrop",
                    toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect | bullist numlist | outdent indent | undo redo | link unlink anchor code | hr table | subscript superscript | charmap | <?php $i=0; echo implode(' | ', array_filter(array_map(function($b)use(&$i){return ($b->options)?'button-'.($i++).$b->text:false;},$buttons))) ?>",
                    skin: 'lightgray',
                    document_base_url: '<?= JUri::root(); ?>',
                    theme : 'modern'
                };

                $scope.changesMade = function(){
                    return false;
                    // TODO: for future
                    if(!$scope.backup_json){
                        if(angular.toJson($scope.questions) != <?= $quests_data ?>){
                            $scope.backup_json = angular.toJson($scope.questions);
                        }
                        console.log($scope.backup_json);
                        return false;
                    }else{
                        return angular.toJson($scope.questions) != $scope.backup_json;
                    }
                };

                $scope.addOpt = function(quest){
                    var tmp = {'text':quest.new_option.text};
                    if(!quest.options.length){
                        tmp.right = true;
                    }
                    quest.options.push(tmp);
                    quest.new_option.text = '';
                };

                $scope.random = function(){
                    return 0.5 - Math.random();
                };

                $scope.parseFloat = function(input){
                    return parseFloat(input).toFixed(2);
                };

                $scope.getScore = function(question){
                    if($scope.partialAvaliable(question)){
                        var points = 0;
                        var correct = 0;
                        var redundant = 0;
                        var total_correct = $scope.countCorrectOptions(question);

                        angular.forEach(question.test_answer, function(value, key) {
                            if(question.options[key].right && value){
                                points += parseFloat(question.options[key].points);
                                correct++;
                            }else{
                                if(value){
                                    points -= parseFloat(question.options[key].points);
                                    redundant++;
                                }
                            }
                        });

                        var remained = total_correct - correct;
                        var missed = remained + redundant;
                        var percentage = (total_correct-missed)/(redundant?question.options.length:total_correct);

                        if(!missed){
                            question.test_result = true;
                        }else{
                            question.test_result = false;
                            if(question.partial && correct){
                                question.test_result_partial = true;
                            }else{
                                question.test_result_partial = false;
                            }
                        }
                        if(question.partial && correct){
                            points += percentage * parseFloat(question.points);
                        }else{
                            if(!missed){
                                points += parseFloat(question.points);
                            }
                        }
                        return points;
                    }else{
                        if(!question.test_answer && question.test_answer!==0 || typeof question.options[question.test_answer] == 'undefined' ){
                            return 0;
                        }
                        if(question.options[question.test_answer].right){
                            question.test_result = true;
                            return question.points;
                        }else{
                            question.test_result = false;
                            return 0;
                        }
                    }
                    return 0;
                };

                $scope.countCorrectOptions = function (question) {
                    return question.options.reduce(function(p,c){
                        if(c.right === true)
                            p++;
                        return p;
                    },0);
                };
                $scope.partialAvaliable = function (question) {
                    return $scope.countCorrectOptions(question) > 1
                };
                $scope.getQuestType = function (question) {
                    return $scope.partialAvaliable(question) ? 'checkbox' : 'radio'
                }
            }])
            .directive('navTabs', function($rootScope){
                return {
                    restrict: 'C',
                    transclude: true,
                    template: '<ng-transclude></ng-transclude>'
                    +
                    '<li ng-repeat="(i, value) in questions" ><a for="sub-question-{{i + 1}}" ng-href="#sub-question-{{i + 1}}">SubQuestion {{i + 1}} <span class="closing-sp"><span class="icon-cancel-circle" ng-click="removeQuestion(i);"></span></span></a></li>'
                    +
                    '<li><a class="ang-trigger" href ng-click="addQuestion();"><i class="icon-plus-2"></i></a></li>',
                    link: function(scope, elem, attrs) {
                        scope.removeQuestion = function(i){
                            $rootScope.questions.splice(i,1);
                        };

                        scope.addQuestion  = function () {
                            $rootScope.questions.push({'options':[]});
                            setTimeout(function () {
                                jQuery('a[for="sub-question-'+ $rootScope.questions.length +'"]').click();
                            },200);
                        };
                    }
                };
            });
        angular.bootstrap(document.getElementById('question-edit'), ['question-edit']);
        $(document).on('click','ul.nav.nav-tabs li > a:not(".ang-trigger")',function(e){
            $($(this).attr('href')).addClass('active').siblings().removeClass('active');
            $(this).closest('ul').find('li').removeClass('active');
            $(this).closest('li').addClass('active');
            e.preventDefault();
            return false;
        });
        $(document).on('keypress','.sbmt-by-enter', function (e) {
            if(e.charCode==13){
                $(this).closest('tr').find('button').click();
                return false;
            }
        });
        $(document).on('keypress','.float-filtering', function (e) {
            if(!(/[0-9\.\-]/.test(e.key))){
                return false;
            }
        });
        $(document).on('keypress','.int-filtering', function (e) {
            if(!(/[0-9\-]/.test(e.key))){
                return false;
            }
        })
        $(document).on('keypress','.only-above-zero', function (e) {
            if(/[\-]/.test(e.key)){
                return false;
            }
        })
    });
</script>
<?php
?>
<div class="alert alert-info" ng-show="changesMade()">
    Unsaved changes present
</div>
<div ng-repeat="(i, question) in questions" id="sub-question-{{i + 1}}" class="tab-pane">
    <div class="row-fluid tabloid">
        <div class="span8">
            <h5>
                Question settings
            </h5>
            <div class="control-group">
                <label class="control-label">Question text</label>
                <div class="controls">
                    <textarea ui-tinymce="tinymceOptions" ng-model="question.text"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Question points</label>
                <div class="controls">
                    <input type="text" ng-model="question.points"/>
                </div>
            </div>
            <div ng-show="question.options.length" class="control-group">
                <label class="control-label">Question attemps</label>
                <div class="controls">
                    <div ng-class="{'input-append':question.attempts==0}">
                        <input type="text" ng-model="question.attempts" class="int-filtering only-above-zero"/>
                        <span class="add-on" ng-show="question.attempts==0">infinitely</span>
                    </div>
                </div>
            </div>
            <div class="alert alert-danger" ng-show="question.attempts==0 && !(question.feedback && question.feedback_incorrect)">
                You should
                <b ng-show="!question.feedback"> enable feedback</b>
                <span ng-show="!question.feedback && !question.feedback_incorrect"> and</span>
                <b ng-show="!question.feedback_incorrect"> provide feedback for incorrect answer</b>
                or your you'll have issues with user experience.
            </div>
            <hr/>
            <h5>
                Options settings
            </h5>
            <div class="control-group" ng-show="partialAvaliable(question)">
                <label class="control-label">Use partial score</label>
                <div class="controls">
                    <input type="checkbox" ng-model="question.partial"/>
                </div>
            </div>
            <div ng-show="question.options.length" class="control-group">
                <label class="control-label">Shuffle options</label>
                <div class="controls">
                    <input type="checkbox" ng-model="question.shuffle"/>
                </div>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        Option
                    </th>
                    <th>
                        Is correct
                    </th>
                    <th ng-show="partialAvaliable(question)">
                        Points
                    </th>
                    <th>

                    </th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="(r, obj) in question.options">
                    <td width="10px">
                        {{r+1}}
                    </td>
                    <td width="30%">
                        <input type="text" ng-model="obj.text">
                    </td>
                    <td width="70px">
                        <input type="checkbox" ng-model="obj.right"/>
                    </td>
                    <td width="30%">
                        <div class="input-prepend" ng-show="partialAvaliable(question)">
                    <span class="add-on span4">
                        <span ng-hide="obj.right">penalty</span>
                        <span ng-show="obj.right">points</span>
                    </span>
                            <input class="span3 float-filtering" type="text" ng-model="obj.points"/>
                        </div>
                    </td>
                    <td>
                        <button ng-click="question.options.splice(r,1)" type="button" class="btn btn-small btn-default">Erase <i class="icon-cancel-circle"> </i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td colspan="3">
                        <input type="text" placeholder="Add new option (type & hit 'Enter')" ng-model="question.new_option.text" class="sbmt-by-enter">
                    </td>
                    <td>
                        <button type="button" class="btn btn-default" ng-click="addOpt(question);">
                            <i class="icon-plus-2"></i> Add
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div ng-show="question.options.length" class="feedbacks">
                <hr/>
                <h5>
                    Feedback
                    <button class="btn btn-small" ng-click="question.feedback = !question.feedback" ng-class="{'btn-default':!question.feedback, 'btn-success':question.feedback}" type="button">{{question.feedback?'Enabled':'Disabled'}}</button>
                </h5>
                <div ng-show="question.feedback">
                    <div class="control-group">
                        <label class="control-label">Correct answer</label>
                        <div class="controls">
                            <textarea ng-model="question.feedback_correct"></textarea>
                        </div>
                    </div>
                    <div class="control-group" ng-show="question.partial">
                        <label class="control-label">Partial answer</label>
                        <div class="controls">
                            <textarea ng-model="question.feedback_partial"></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Incorrect answer</label>
                        <div class="controls">
                            <textarea ng-model="question.feedback_incorrect"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="span4">
            <h3>Test the question</h3>
            <div class="well">
                <p ng-bind-html="question.text"></p>
                <div class="" ng-init="question.test_answer = null">
                    <div ng-repeat="(r, obj) in question.options">
                        <label ng-if="getQuestType(question)=='checkbox'">
                            <input type="checkbox" ng-model="question.test_answer[r]" value="{{r}}" name="test_answer_{{i}}" /> {{obj.text}}
                        </label>
                        <label ng-if="getQuestType(question)=='radio'">
                            <input type="radio" ng-model="question.test_answer" value="{{r}}" name="test_answer_{{i}}" /> {{obj.text}}
                        </label>
                    </div>
                </div>
                <div class="feedbacks-test" ng-show="question.test_answer!==null && question.feedback" ng-init="question.test_result=false; question.test_result_partial=false;">
                    <br/>
                    <div class="alert" ng-class="{'alert-success':question.test_result,'alert-warning':!question.test_result && question.test_result_partial,'alert-danger':!question.test_result_partial && !question.test_result}" ng-bind-html="question.test_result?question.feedback_correct:(question.test_result_partial && question.feedback_partial ?question.feedback_partial:question.feedback_incorrect)">
                    </div>
                </div>
            </div>
            <h5 ng-show="question.options.length">Your score is: {{parseFloat(getScore(question))}}</h5>
        </div>
    </div>
    <input type="hidden" name="subquestion[{{question.id}}]" value="{{question}}"/>
</div>