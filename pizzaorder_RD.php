<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pizza bestellen</title>
        <style>
            * {
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }
            body {
                display: grid;
                background-image: url(https://thumbs.dreamstime.com/z/pizza-seamless-pattern-vector-pizza-pattern-abstract-background-background-useful-restaurant-identity-packaging-89205691.jpg);
                /* 
                    Royalty free illustration version (watermark)
                    https://www.dreamstime.com/stock-illustration-pizza-seamless-pattern-vector-pizza-pattern-abstract-background-background-useful-restaurant-identity-packaging-image89205691 
                */
            }
            main {
                display: contents;
                grid-area: main;
                
            }

            #header {
                background-color: rgba(51, 153, 0,0.98);
                grid-area: head;
                padding: 10px;                
            }

            h1 {
                margin-top: 10px;
                font-size: 2rem;
            }
            form {
                background-color: rgba(102, 153, 153, 0.98);
                padding: 15px;
                grid-area: form;
            }
            form div {
                padding-bottom: 5px;
                padding-top: 5px;
            }

            input, select {               
                border-radius: 10px;
                border: 1px solid black;
                padding: 8px;               
            }

            input[type=text]:hover, input[type=number]:hover, #btns input:hover, select:hover{
                box-shadow: 0px 0px 3px 0px #000000;
            }

            #radio label{
                display: inline-block;
            }          

            #textfield label{
                width: 100px;
                display: inline-block;
            }

            #textfield > * {
                margin-bottom: 10px;
            }

            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            #pizza {
                padding-left: 5px;
            }

            #extras {
                padding-left: 20px;
            }

            #extras label input{
                margin-bottom: 10px;
            }

            #time div{
                display: inline-block;
            }
            
            #btns input{
                margin-left: 10px;
                margin-right: 10px;
                border: 1px solid black;
            }

            #result1 {
                text-align: center;               
                grid-area: head;
                background-color: rgba(102, 153, 153, 0.98);
                padding: 0px 10px 0px 10px;
            }

            #result2 {
                text-align: center;
                grid-area: head;
                padding: 0px 10px 0px 10px;
                background-color: rgba(102, 153, 153, 0.98);
            }

            #resultend {
                text-align: center;
                grid-area: form;
                background-color: rgba(102, 153, 153, 0.98);
            }

            @media screen and (min-width: 0px) and (max-width: 640px) {
                body {
                    grid-template-columns: 1fr;
                    grid-template-areas: 
                        'head'
                        'form';
                }
            } 

            @media screen and (min-width: 641px) and (max-width: 960px){
                body {
                    grid-template-columns: repeat(5,1fr);
                    grid-template-areas: 
                        '. head head head .'
                        '. form form form .';
                }
            }

            @media screen and (min-width: 961px) {
                body {
                    grid-template-columns: repeat(5,1fr);
                    grid-template-areas: 
                        '. head head head . '
                        '. form form form .';
                }
            }
        </style>
    </head>
    <body>                  
        
        <main>
            <?php
                //require_once './util.php';

                /*        Functions       */
                function outputSelectElement($name,$id,$options,$default='') {
    
                    echo '<div>Pizza: <select name="'.$name.'" id="'.$id.'">';

                        foreach ($options as $index => $value) {
                            echo '<option value="'.$index.'" '.( ($index==$default) ? 'selected': '' ).'> '.$index.': '.$value.' EUR</option>';
                        }
                    echo '</select></div>';
                }

                function mostexpensiveSelectOption($options) {

                    $maxprice = 0;
                    $preferred = '';

                    foreach ($options as $index => $value) {
                        if($value > $maxprice){
                            $maxprice = $value;
                            $preferred = $index;
                        }
                    }

                    return $preferred;
                }

                function extraToppings($extra){
                    foreach ($extra as $key => $value) {
                        echo '<label><input type="checkbox" name="'.$key.'">'.$key.'( +'.$value.' €)</label><br>';
                    }
                }

                function timeSettings($startHH,$endHH,$interval){
                    $time_array = [];
                    for ($i=$startHH; $i < $endHH; $i++) { 
                        for ($j=0; $j < 60; $j+=$interval) {
                            $time_array[] = str_pad($i,2,'0',STR_PAD_LEFT).':'.str_pad($j,2,'0',STR_PAD_LEFT);
                        }
                    }
                    return $time_array;
                }

                function cleanData(&$data) {

                    $data = strip_tags($data);

                    $data = trim($data);
                }
                
                /*      DATA       */

                $gender = ['Frau', 'Herr', 'Hallo'];

                // can deliver to this PLZ
                $validPlz = [1030,1040,1050,1060,1070,1080,1100,1110,1120,1150];

                //  add/remove pizza and price here
                $pizzen = [
                    'Salami' => 8.5,
                    'Margharita' => 8,
                    'Prosciutto' => 8.5, 
                    'Funghi' => 8.5, 
                    'Hawaii' => 9, 
                    'Fritti di Mare' => 9.4, 
                    'al Formaggi' => 9];

                // add/remove extra toppings and price here
                $extras = [
                    'Champignon' => 2, 
                    'Knoblauch' => 1.5,
                    'Mozarella' => 1.5, 
                    'Schinken' => 2, 
                    'Spinat' => 1, 
                    'Zwiebel' => 1];
                
                // change time settings here: begin, end(excluded), interval
                $times = timeSettings(8,20,15);


                if (isset($_POST['form']) && ($_POST['form']=='order')) {

                    array_walk($_POST,'cleanData');                    

                    $price = 0;
                    $chosenToppings = [];

                    if(in_array($_POST['gender'],$gender) && 
                        in_array($_POST['plz'],$validPlz) && 
                        array_key_exists($_POST['pizza'],$pizzen) &&                        
                        in_array($_POST['time'],$times)){

                            // checkbox checked + update price
                            foreach ($extras as $key => $value) {                        
                                if(array_key_exists($key,$_POST)){                           
                                    $chosenToppings[$key] = $value;
                                    $price += $value;
                                }
                            }

                            $price += $pizzen[$_POST['pizza']];

                            echo '<div id="result1">';
                            echo '<p>'.$_POST['gender'].' '.$_POST['name'].',</p>';
                            echo '<p>Danke für Ihre Bestellung.</p>';
                            echo 'Ihre Pizza '.$_POST['pizza'];

                            if(count($chosenToppings) !=0){
                                echo ' mit';
                                foreach ($chosenToppings as $key => $value) {                                                  
                                    echo ' extra '.$key.' - ';
                                }
                            }

                            echo '<br>liefern wir auf die folgende Adresse: '.$_POST['adress'].', '.$_POST['plz'].' um '.$_POST['time'].'.<br><br>';
                            echo ' Der Gesamtpreis beträgt: '.$price.' €.';
                            echo '</div>';
                    }
                    else {
                        echo '<div id="result2"><p>Falsche Eingaben erhalten. Bitte Eingaben überprüfen.</p></div>';
                    }
                    
                    echo '<div id="resultend"><p> - Ihre WIFI - Pizzeria</p></div>';
                    
                    
                }
                else { // else begin
            ?> 
                <div id="header">
                    <h1>Willkommen bei WIFI Pizzeria</h1>
                    <p>Lieferung nur nach: 1030, 1040, 1050, 1060, 1070, 1080, 1100, 1110, 1120, 1150 möglich</p>       
                </div>
                <form id="order" method="POST" >
                    <div id="radio">
                        <label><input type="radio" name="gender" value="Frau" required>Frau</label> 
                        <label><input type="radio" name="gender" value="Herr" >Herr</label>
                        <label><input type="radio" name="gender" value="Hallo" >Sonstiges</label>
                    </div>
                    
                    <div id="textfield">
                        <label for="name">Name:</label>
                        <input type="text" name="name" size="30" maxlength="50" placeholder="Name eingeben" required>
                        <br>
                        <label for="adress">Adresse:</label>
                        <input type="text" name="adress" size="30" placeholder="Adresse eingeben" required>
                        <br>
                        <label for="plz">Postleizahl:</label>
                        <input type="number" name="plz" min="1000" max="9999" placeholder="Plz" required>
                    </div>
                   
                    <div id="pizzaSelection">
                        <?php
                            outputSelectElement('pizza','pizza',$pizzen,mostexpensiveSelectOption($pizzen));
                        ?>
                    </div>

                    <div id="extras">
                        <div>Extras: </div>
                        <?php
                            extraToppings($extras);
                        ?>
                    </div>

                    <div id="time">
                        <div>Uhrzeit: </div>
                        <?php                           
                            echo '<select name = "time">';
                            for ($i=0; $i < count($times); $i++) { 
                                echo '<option value ="'.$times[$i].'">'.$times[$i].'</option>';
                            }
                            echo '</select>';
                        ?>
                    </div>

                    <div id="btns">
                        <input type="hidden" value="order" name="form">
                        <input type="submit" value="Absenden">
                        <input type="reset" value="Zurücksetzen">
                    </div>
                </form>
            <?php
                } //else end
            ?>        
        </main>
    </body>
</html>