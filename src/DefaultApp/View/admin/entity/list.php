
<?php
    $_title = $entityAdmin->name." | Liste";
?>

<?php ob_start(); ?>
    <style>
        #list{
            width:100%;
        }
        #list th{
            background:rgb(200,200,200);
            border-bottom:1px solid rgb(170,170,170);
            text-align:left;
            text-transform: uppercase;
            font-size: 12px;
        }
        #list .action{
            display:inline-block;
            background:var(--secondary-color);
            color:white;
            padding:5px;
            margin:2px;
        }
        main{
            position:relative;
        }
        #filter-pane{
            width:100%;
        }
        #filter-pane #filters{
            background:white;display:none;
            border-left: 3px solid var(--primary-color);
            padding:5px;
        }
        #list{

        }
        #filter-pane h3{
            margin: 0;padding:5px 0;
            display:flex;
            justify-content: space-between;
        }
        #filter-pane h3 span{
            cursor:pointer;
            transition:all .2s ease;margin-right:5px;
        }
        #filter-pane h3 span:hover{
            color:var(--primary-color);
            transform:scale(1.7);
        }
        #filter-pane label{
            display:inline-block;width:120px;vertical-align: center;
        }
        #filter-pane select, #filter-pane input[type="text"], #filter-pane input[type="password"], #filter-pane input[type="number"]{
            height: 30px;
            font-family:calibri;
            width:200px;
        }

        #filter-pane select:hover, #filter-pane input[type="text"]:hover, #filter-pane input[type="password"]:hover, #filter-pane input[type="number"]:hover,
        #filter-pane select:focus, #filter-pane input[type="text"]:focus, #filter-pane input[type="password"]:focus, #filter-pane input[type="number"]:focus{
            color:var(--primary-color);
        }
        .filter{
            margin:3px 0;
        }

    </style>
<?php $_styles = ob_get_clean(); ?>

<?php ob_start(); ?>
    <?php if(count($filterFields) > 0){ ?>
    <div id="filter-pane">
        <h3><font>Filtres</font> <span>&#10095;&#10095;</span><span style="display:none">&#10094;&#10094;</span></h3>
        <div id="filters">
            <?php 
                foreach($filterFields as $fieldName){ 
                    echo $formFields[$fieldName]->getHTMLForFilter();
                } 
            ?>
        </div>
    </div>
    <?php } ?>
    <table id="list" cellspacing="0" cellpadding="3">
        <thead>
            <tr><!-- Top -->
                <td colspan="<?= count($mainFields)+2 ?>">
                    <button id="deleteSelectionBtn" class="btn btn-disable cursor-pointer" style="margin: 3px 0">Supprimer la selection</button>
                </td>
            </tr>
            <tr>
                <th></th><!-- check case -->
                <?php foreach($mainFields as $fieldName => $field){ ?>
                    <th><?= $formFields[$fieldName]->getLabel(); ?></th>
                <?php } ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($elements as $element){ ?>
                <tr>
                    <td><input type="checkbox" class="line-checkbox" data-key="<?= $element->$keyProperty ?>"/></td>
                    <?php foreach($mainFields as $fieldName => $field){ $value = $element->$fieldName;?>
                        <td class="<?= $fieldName ?>">
                            <?php
                                if($field["type"] == "EnumField")
                                    echo $formFields[$fieldName]->getElements()[$value] ?? "";
                                else{
                                    if($value instanceof \DateTime) 
                                        echo $value->format("d-m-Y"); 
                                    else
                                        echo (is_array($value)) ? implode(" | ",$value) : $value;
                                    
                                }
                                    
                            ?>
                        </td>
                    <?php } ?>
                    
                    <td>
                        <a href="<?= $url("admin-entity-update", ["entityName" => $entityAdmin->name, "key" => $element->$keyProperty]) ?>" class="action">Afficher</a>
                        <?php if($adminGroupPermission->canDelete){ ?>
                            <a href="<?= $url("admin-entity-delete", ["entityName" => $entityAdmin->name, "key" => $element->$keyProperty]) ?>" class="action">Supprimer</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php $_main = ob_get_clean(); ?>

<?php ob_start(); ?>
    <script type="text/javascript">
        <?php include ASSETS."/js/jquery.js"; ?>
    </script>
    <script type="text/javascript">


        $('#filter-pane h3 span').click(function(){
            $('#filter-pane #filters').slideToggle(500);
            $('#filter-pane h3 span').toggle(-1);
        });
        /**
         * object {fieldName: function(value)}
         */
        let filtersRules = {};
        $('.filter').each(function(){
            let type = $(this).attr("data-field-class");
            let fieldName = $(this).attr("data-field-name");
            if(type ==  "Number"){
                let $minInput = $(this).find("input[data-type='min']");
                let $maxInput = $(this).find("input[data-type='max']");
                $minInput.keyup(applyFilter);
                $maxInput.keyup(applyFilter);
                filtersRules[fieldName] = function(value){
                    value = parseFloat(value);
                    if($minInput.val() == "" && $maxInput.val() == "")
                        return true;
                    else if($minInput.val() != "" && $maxInput.val() == "")
                        return value >= parseFloat($minInput.val());
                    else if($minInput.val() == "" && $maxInput.val() != "")
                        return value <= parseFloat($maxInput.val());
                    else
                        return value >= parseFloat($minInput.val()) && value <= parseFloat($maxInput.val());
                }
            }else if(type ==  "Select" || type ==  "Relation"){ 
                let $select = $(this).find("select");
                $select.change(applyFilter);
                filtersRules[fieldName] = function(value){
                    if($select.val() == '')
                        return true;
                    return value.trim() == $select.val().trim();
                }
            }else if(type ==  "Checkbox"){
                let $select = $(this).find("select");
                $select.change(applyFilter);
                filtersRules[fieldName] = function(value){
                    console.log(value);
                    if($select.val() == '')
                        return true;
                    return (value.trim() == $select.val()) || (value.trim() == '' && $select.val() == "0");
                }
            }else{
                let $input = $(this).find("input");
                $input.keyup(applyFilter);
                filtersRules[fieldName] = function(value){
                    return (new RegExp($input.val(), "i")).test(value);
                }
            } 
        });
        function applyFilter(){
            $("#list tbody tr").each(function(){
                let correct = true;
                for(fieldName in filtersRules){
                    if(!filtersRules[fieldName]($(this).find("."+fieldName).text())){
                        correct = false;
                        break;
                    }
                }
                if(correct){
                    $(this).show(-1);
                }else{
                    $(this).hide(-1);
                }
            });
        }
        applyFilter();


        let nbCheckedLines = 0;
        $('.line-checkbox').each(function(){
            $(this).click(function(){
                if($(this).prop("checked")){
                    nbCheckedLines++;
                }else{
                    nbCheckedLines--;
                }
                if(nbCheckedLines <= 0){
                    $("#deleteSelectionBtn").removeClass("btn-secondary").addClass("btn-disable")
                }else{
                    $("#deleteSelectionBtn").addClass("btn-secondary").removeClass("btn-disable")
                }
            });
        });
        $("#deleteSelectionBtn").click(function(){
            if(nbCheckedLines <= 0){
                alert("Aucune ligne sélectionnée");
            }else{
                if(confirm("Les lignes sélectionnées vont être supprimées")){
                    let selectedIds = [];
                    $('.line-checkbox').each(function(){
                        if($(this).prop("checked")){
                            selectedIds.push($(this).attr("data-key"));
                        }
                    });
                    window.location="<?= $url("admin-entity-delete-many", ["entityName" => $entityAdmin->name]) ?>?keys="+selectedIds.join("-");
            
                }    
            }
        });
    </script>
<?php $_scripts = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>