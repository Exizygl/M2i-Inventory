<?php
session_start();
include("componant/config.php");

if (isset($_POST['input'])) {
    $input = $_POST['input'];
    
    if ($input) {

        for ($i = 0; $i <= $input; $i++) {
            
?>
                <div class="form-group">
                    <label for="inputCodeBar"> Code bar: </label>
                    <input type="text" id="inputCodeBar" name="codeBar" value="<?= isset($code) ? $code : "" ?>">
                </div>
                <input type="checkbox" id="inputDisponible" name="disponible" value="<?= isset($dispo) ? $dispo : "" ?>" />
            <?php
            }

            ?>



<?php
        }
    }

