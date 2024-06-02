<?php 
    require "../backend/response_post.php"; 

    if (!$username) {
        header("Location: page_login.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <link rel="stylesheet" href="../styles/style_account.css">
</head>
<body>
    <div id="big_container">
        <div id="container">
            <div id="navbar">
                <div id="navbar_ustbar">
                    <div id="ustbar_bas">
                        <button id="ustscroll_solbuton"><</button>              
                    </div>
                    <div id="ustbar_orta">
                        <button id="hesap_btn" onclick="ToolAc(this, 'tools_hesap');"><?php echo $username;?></button>                   
                        <button id="giris_btn" class="ta_active" onclick="ToolAc(this, 'tools_giris');">Giris</button>
                        <button id="cizim_btn" onclick="ToolAc(this, 'tools_cizim');">Cizim</button>
                        <button id="ekle_btn" onclick="ToolAc(this, 'tools_ekle');">Ekle</button>                  
                    </div>
                    <div id="ustbar_son">
                        <button id="ustscroll_sagbuton">></button>
                    </div>
                </div>
                <div id="navbar_altbar">
                    <div id="altbar_bas">
                        <button id="scroll_solbuton"><</button>              
                    </div>
                    <div id="altbar_orta">
                        <div id="tools_hesap" class="tools">
                            <form method="post" id="logoutForm" style="display:flex">
                                <button type="submit" style="font-size: small;" id="cikis_hesap_btn" name="logout" class="toolbox toolbox_v2">LOGOUT</button>
                            </form>
                            <button style="font-size: small;" id="sifredegis_hesap_btn" class="toolbox toolbox_v2" onclick="MenuAc('sifredegis');">Sifre Degistir</button>
                        </div>
                        <div id="tools_giris" class="tools">
                            <button class="toolbox" onclick="MenuAc('notolustur');" id="menuac_yeninot">+</button>
                            <button class="toolbox" onclick="MenuAc('altnotolustur');" id="menuac_altyeninot">++</button>
                            <button class="toolbox" onclick="GeriAl();">↩</button>
                            <button class="toolbox" onclick="IleriAl();">↪</button>
                            <button class="toolbox" onclick="RemoveStyles()">Clr</button> 
                            <!-- <button class="toolbox" onclick="copyStyle()">Cpy</button>   -->
                            <button id="renk_btn" class="toolbox"  onclick="Color_type('bcolor');ToolAc(null,'tools_renk');"><div style="background-color: black; width: 20px; height: 20px; margin: auto;">C</div></button>
                            <button style="color: coral ;" id="renk_btn" class="toolbox"  onclick="Color_type('color');ToolAc(null,'tools_renk');">C</button>
                            <button style="text-decoration: underline;" class="toolbox" onclick="AltiniCiz()">A</button> 
                            <button style="font-style: italic;" class="toolbox" onclick="Italiklestir()">T</button>
                            <button style="font-weight: bold;" class="toolbox" onclick="Kalinlastir()">K</button>  
                            <button  class="toolbox" onclick="FontSize('7')">h1</button> 
                            <button  class="toolbox" onclick="FontSize('6')">h2</button>
                            <button  class="toolbox" onclick="FontSize('5')">h3</button>  
                            <button  class="toolbox" onclick="FontSize('4')">h4</button> 
                            <button  class="toolbox" onclick="FontSize('3')">h5</button>
                            <button  class="toolbox" onclick="FontSize('2')">h6</button> 
                            <button  class="toolbox toolbox_v2" onclick="Test_Post()">TEST</button>   
                            <!-- <button  class="toolbox" onclick="SubYazi()">A<span style="vertical-align: sub;">2</spans></button>    
                            <button  class="toolbox" onclick="SuperYazi()">A<span style="vertical-align: super;">2</spans></button> -->
                        </div>
                        <div id="tools_renk" class="tools">
                            <button class="toolbox" onclick="ToolAc(null, 'tools_giris');"><</button>
                            <button style="background-color: white; color: black;" class="toolbox" onclick="RenkDegistir('transparent');">T</button>
                            <button style="background-color: #c8c8c8;" class="toolbox" onclick="RenkDegistir('#c8c8c8');"></button>
                            <button style="background-color: white;" class="toolbox" onclick="RenkDegistir('white');"></button>
                            <button style="background-color: black;" class="toolbox" onclick="RenkDegistir('black');"></button>
                            <button style="background-color: red;" class="toolbox" onclick="RenkDegistir('red');"></button>
                            <button style="background-color: green;" class="toolbox" onclick="RenkDegistir('green');"></button>
                            <button style="background-color: blue;" class="toolbox" onclick="RenkDegistir('blue');"></button>
                            <button style="background-color: yellow;" class="toolbox" onclick="RenkDegistir('yellow');"></button>
                            <button style="background-color: cyan;" class="toolbox" onclick="RenkDegistir('cyan');"></button>
                            <button style="background-color: magenta;" class="toolbox" onclick="RenkDegistir('magenta');"></button>
                            <button style="background-color: purple;" class="toolbox" onclick="RenkDegistir('purple');"></button>
                            <button style="background-color: orange;" class="toolbox" onclick="RenkDegistir('orange');"></button>
                            <button style="background-color: gray;" class="toolbox" onclick="RenkDegistir('gray');"></button>
                            <button style="background-color: yellowgreen;" class="toolbox" onclick="RenkDegistir('yellowgreen');"></button>
                            <button style="background-color: brown;" class="toolbox" onclick="RenkDegistir('brown');"></button>
                            <button style="background-color: lime;" class="toolbox" onclick="RenkDegistir('lime');"></button>
                            <button style="background-color: turquoise;" class="toolbox" onclick="RenkDegistir('turquoise');"></button>
                            <button style="background-color: plum;" class="toolbox" onclick="RenkDegistir('plum');"></button>
                            <button style="background-color: cornflowerblue;" class="toolbox" onclick="RenkDegistir('cornflowerblue');"></button>
                        </div>
                        <div id="tools_cizim" class="tools">
                            <div class="toolbox">c1</div>
                            <div class="toolbox">c3</div>
                            <div class="toolbox">c4</div>
                            <div class="toolbox">c5</div>
                            <div class="toolbox">c6</div>
                            <div class="toolbox">c7</div>
                            <div class="toolbox">c8</div>
                            <div class="toolbox">c9</div>
                        </div>
                        <div id="tools_ekle" class="tools">
                            <div class="toolbox">e1</div>
                            <div class="toolbox">e2</div>
                            <div class="toolbox">e3</div>
                            <div class="toolbox">e4</div>
                            <div class="toolbox">e5</div>
                        </div>
                    </div>
                    <div id="altbar_son">
                        <button id="scroll_sagbuton">></button>
                    </div>      
                </div>
            </div>
            <div id="container_nonav_overflow">
                <div id="container_nonav">
                    <div id="govde">
                        <div id="notlar" style="width: <?php echo $sorgu_1->notlar_width; ?>px;">
                            <ul id='notlar_ul' onmouseleave="dragStop(true);">
                                <?php
                                    echo Not_Listele();
                                ?>
                            </ul>
                        </div>
                        <div id="resizer"></div>
                        <div id="icerik">
                            <div id="icerik_baslik">
                                <div id="not_baslik" spellcheck="false"                             
                                <?php 
                                    if($activenot != ""){
                                        echo "not_uindex='" . $activenot->not_uindex . "'";
                                    }
                                ?>                               
                                >
                                    <?php 
                                        if($activenot != ""){
                                            echo $activenot->baslik;
                                        }
                                        else{
                                            echo ""; //baslik_degistir_btn enable false yap 
                                        }
                                    ?>
                                </div>
                                <button id="baslik_degistir_btn">!</button>
                                <button id="notu_kaydet_btn" onclick="Notu_Kaydet_Post();" disabled>K</button>
                                <button id="notu_sil_btn" onclick="Notu_Sil_Post();">S</button>
                            </div>
                            <div id="icerik_icerik" contenteditable="true" spellcheck="false" icerikdegisti="false">
                                <?php 
                                    if($activenot != ""){
                                        echo $activenot->icerik;
                                    }
                                    else{
                                        echo ""; //icerik_icerik contenteditable false yap 
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div id="footer"></div>
                </div> 
            </div> 
        </div>

        <div id="hesap_container">
            <div id="karart_container"></div>
            <div id="hesap_menu_overflow">
                <!-- <div id="menu_sifredegis">
                    <button class="menukapat_btn" onclick="MenuKapat();">X</button>
                    <form id="sifredegisForm">
                        <input class="menu_inputbox" type="password" placeholder="eski sifre gir">
                        <input class="menu_inputbox" type="password" placeholder="yeni sifre gir">
                        <input class="menu_inputbox" type="password" placeholder="yeni sifre tekrar gir">
                        <input type="button" value="Sifre Degistir">
                    </form>
                </div> -->
                <!-- <div id="menu_notolustur">
                    <button class="menukapat_btn" onclick="MenuKapat();">X</button>
                    <form id="notolusturForm">
                        <input type="text" class="menu_inputbox" id="not_ismi" name="not_ismi" placeholder="Not ismi" onkeydown="MenuPostYolla(event , 'notolustur');">
                        <button type="button" onclick="Not_Olustur_Post();">Not Olustur</button>
                    </form>
                </div> -->
                <!-- <div id="menu_altnotolustur">
                    <button class="menukapat_btn" onclick="MenuKapat();">X</button>
                    <form id="altnotolusturForm">
                        <input type="text" class="menu_inputbox" id="altnot_ismi" name="altnot_ismi" placeholder="Alt Not ismi" onkeydown="MenuPostYolla(event , 'altnotolustur');">
                        <button type="button" onclick="Altnot_Olustur_Post();">Alt Not Olustur</button>
                    </form>
                </div> -->
                <div id="menu_sifredegis">
                    <div class="menu_baslik">Sifre Degistir</div>
                    <form id="sifredegisForm">
                        <input type="text" class="menu_inputbox" placeholder="Eski sifre gir" >
                        <input type="text" class="menu_inputbox" placeholder="Yeni sifre gir" >
                        <input type="text" class="menu_inputbox" placeholder="Yeni sifre tekrar gir" >
                        <div class="menu_altbtns">
                            <button class="menu_btns" type="button" onclick="MenuKapat();">Iptal Et</button>
                            <button class="menu_btns" type="button" >Degistir</button> 
                        </div>    
                    </form>
                </div>
                <div id="menu_notolustur">
                    <div class="menu_baslik">Not Olustur</div>
                    <form id="notolusturForm">
                        <input type="text" class="menu_inputbox" id="not_ismi" name="not_ismi" placeholder="Not ismi" onkeydown="MenuPostYolla(event , 'notolustur');">
                        <div class="menu_altbtns">
                            <button class="menu_btns" type="button" onclick="MenuKapat();">Iptal Et</button>
                            <button class="menu_btns" type="button" onclick="Not_Olustur_Post();">Olustur</button> 
                        </div>    
                    </form>
                </div>
                <div id="menu_altnotolustur">
                    <div class="menu_baslik">Alt Not Olustur</div>
                    <form id="altnotolusturForm">
                        <input type="text" class="menu_inputbox" id="altnot_ismi" name="altnot_ismi" placeholder="Alt Not ismi" onkeydown="MenuPostYolla(event , 'altnotolustur');">
                        <div class="menu_altbtns">
                            <button class="menu_btns" type="button" onclick="MenuKapat();">Iptal Et</button>
                            <button class="menu_btns" type="button" onclick="Altnot_Olustur_Post();">Olustur</button> 
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="../js/script_account.js"></script>
    <script src="../js/ajax_request.js"></script>
    <script src="../js/ajax_response.js"></script>
    <script>
        function GeriAl(){
            document.execCommand('undo');
        }

        function IleriAl(){
            document.execCommand('redo');
        }

        function RemoveStyles() {
            document.execCommand("removeFormat", false, null);           
        }

        let color_type = "color"  //bcolor - color
        function Color_type(_color_type){
            color_type = _color_type;
        }
        function RenkDegistir(_renk) {
            if(color_type == "color"){
                document.execCommand("styleWithCSS", null, true);
                document.execCommand("foreColor", false, _renk);
            }
            else if(color_type == "bcolor"){
                document.execCommand("styleWithCSS", false, true);
                document.execCommand("hiliteColor", false, _renk);
            }          
            ToolAc(null, 'tools_giris');
        }

        function AltiniCiz() {
            document.execCommand("styleWithCSS", false, true);
            document.execCommand("underline", false, null);
        }

        function Italiklestir() {
            document.execCommand("styleWithCSS", false, true);
            document.execCommand("italic", false, null);
        }

        function Kalinlastir() {
            document.execCommand("styleWithCSS", null, true);
            document.execCommand("bold", false, null);
        }

        function FontSize(_fontsize) {
            document.execCommand("styleWithCSS", false, true);    
            document.execCommand('fontSize', false, _fontsize);
        }

        // function SubYazi(){
        //     document.execCommand("subscript", false, null);
        // }

        // function SuperYazi(){
        //     document.execCommand("superscript", false, null);
        // }
    </script>
</body>
</html>