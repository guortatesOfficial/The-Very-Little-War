<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); header('Content-type: text/html; charset=utf-8');?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PlanetHoster - Votre hébergement est actif!</title>
  <meta name="description" content="PlanetHoster solutions d'hébergement web, que ce soit des hébergements mutualisés, des plans revendeurs, des serveurs dédiés, des serveurs virtuels ou de l'hébergement E-commerce">
  <meta name="author" content="PlanetHoster Inc.">
  <link rel="icon" href="//www.planethoster.net/favicon.ico" type="image/x-icon" />
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<style type="text/css">
  html{
    height: 100%;
  }

  body{
   height: 100%;
   min-height: 100%;
   margin: 0;
   padding: 0;
   background-color: white;
   font-family: 'Open Sans', arial, verdana, helvetica, sans-serif;
   text-align: center;
   color: #4f4f4f;
   font-size: 16px;
   background: transparent url(//cdn.planethoster.net/welcome/planet_back.png) no-repeat center top;
   background-size: 60% auto;
  }

  #wrapper{
    min-height:100%;
    position:relative;
  }

  #content{
    padding-bottom: 0.5em;
  }

  .container{
    max-width: 1170px;
    margin: 0 auto;
    padding: 0 1em;
  }

  a{text-decoration: none;}

  #header-title{
    /*background-color: #e6e7e8;*/
  }

  h1{
    font-weight: normal;
    font-size: 1.5em;
    padding: 1.5em 0;
    margin: 0;
  }
    h1 a{color: #7ba940; font-size: 1.25em; font-weight: bold;}

  h2{padding: 0.5em 0 1em 0; margin: 0; font-size: 2em; color: white; font-weight: normal;}
    h2 strong{}

  header{
    padding: 0.5em 0;
    background-color: #4f4f4f;
    text-align: left;
  }

  #title-step{
    position: relative;
    background-color: #7ba940;
  }
    #title-step #title-step-triangle{
      position: relative;
      display: inline-block;
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 20px 20px 0 20px;
      border-color: rgba(255, 255, 255, 0.9) transparent transparent transparent;
    }

  #step-container{
    overflow: hidden;
    margin-bottom: 4em;
  }

  #step-container .step{
    position: relative;
    float: left;
    width: 33%;
    height: 25em;
    margin-right: 2px;
  }
    #step-container .step:last-child{
      margin-right: 0;
    }

     #step-container .step h3{
      margin: 0;
      color: #f26c31;
      font-size: 6em;
     }

     #step-container .step .step-txt{
      display: table-cell;
      vertical-align: middle;
      height: 5em;
      padding: 0 1em;
      border-left: 1px solid #7ba940;
      font-weight: bold;
     }
      #step-container .step:last-child .step-txt{
        border-right: 1px solid #7ba940;
      }

     #step-container .step .step-img-logo{
      width: 40%;
      display: block;
      margin: 1.5em auto;
      transition: transform 500ms cubic-bezier(0.490, -0.600, 0.555, 1.650);
     }

     #step-container .step .step-img-logo:hover{
        transform: scale(1.25,1.25);
     }

      #step-container #step-1 .step-img-logo{
        width: 26%;
      }
       #step-container #step-2 .step-img-logo{
        width: 30%;
       }

     #step-container .step .step-cta{
      display: block;
      width: 100%;
      position: absolute;
      bottom: 0;
      padding: 0.5em 0;
      background-color: #f26c31;
      color: white;
      font-weight: bold;
      transition: background-color 500ms;
     }
      #step-container .step .step-cta:hover{
        background-color: #4f4f4f;
      }

  footer{
    position:absolute;
    bottom: 0;
    width: 100%;
    height: 0.5em;
    padding: 1.5em 0;
    background-color: black;
    overflow: hidden;
    color: white;
    text-align: left;
  }

    footer a{
      color: white;
      transition: color 500ms;
    }
      footer a:hover{
        color: #7ba940;
      }

    footer .container{margin-top: -0.2em;}

    footer .section{
      float: left;
      width: 33.3333333333%;
      font-size: 0.75em;
    }
      footer .section.section-copyright{text-align: center;}
      footer .section.section-social{position: relative; top: -9px; text-align: right;}

    footer .ph-social-4icons {
      position: relative;
      top: 6px;
      background: url(//cdn.planethoster.net/welcome/footer_reseaux_sociaux.png) no-repeat;
      background-size: 88px 44px;
      display: inline-block;
      width: 22px;
      height: 22px;
      margin-right: 10px;
    }
      footer .ph-social-4icons.ph-social-facebook {background-position: -0px -0px;}
      footer .ph-social-4icons.ph-social-twitter {background-position: -22px -0px;}
      footer .ph-social-4icons.ph-social-googlep {background-position: -44px -0px;}
      footer .ph-social-4icons.ph-social-linkedin {background-position: -66px -0px;}

    @media screen and (max-width: 1124px) {  
      footer .section.section-social span{
        display: none;
      }
    }     


  @media screen and (max-width: 767px) {
    
    body{
      background-size: 100% auto;
    }

    #content{
      padding-bottom: 3.5em;
    }

    header{text-align: center;}

    #step-container .step{
      float: none;
      width: 100%;
      height: auto;
      padding-bottom: 3em;
      border-bottom: 1px solid #7ba940;
    }

      #step-container .step .step-txt{
        display: block;
        height: auto;
        border: 0;
      }
        #step-container .step:last-child .step-txt{
          border-right: 0;
        }

      
      #step-container .step .step-cta{
        max-width: 50%;
        margin: 0 auto;
        position: inherit;
      }

      footer{height: 3.5em; font-size: 1em;}
      footer .section{
        width: 100%;
        text-align: center;
      }
        footer .section.section-social{
          margin-top: 1em;
          text-align: center;
        }
          footer .section.section-social span{
            display: none;
          }
  }

  @media screen and (max-width: 460px) {
    body{
      font-size: 12px;
    }
    h1{
      padding: 2em 0;
    }
    header img{
      max-width: 80%;
      height: auto;
    }
    #step-container .step .step-cta{
      max-width: inherit;
      width: 100%;
    }
  }
</style>
</head>
<body>
<div id="wrapper">
  <header>
    <div class="container">
      <a href="https://www.planethoster.net"><img src="//cdn.planethoster.net/welcome/planethoster-logo.png" width="150"></a>
    </div>
  </header>
  <div id="content">
    <div id="header-title">
      <div class="container">
        <h1 id="title">Votre Hébergement <a href="http://<?php if (!(preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $_SERVER['SERVER_NAME']))) echo "<a href='http://".$_SERVER['SERVER_NAME']."'>".$_SERVER['SERVER_NAME']."</a>"; ?>"><?php if (!(preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $_SERVER['SERVER_NAME']))) echo "<a href='http://".$_SERVER['SERVER_NAME']."'>".$_SERVER['SERVER_NAME']."</a>"; ?></a> est maintenant actif !</h1>
      </div>
    </div>
    <div id="title-step">
      <div class="container">
        <i id="title-step-triangle"></i>
        <h2>QUE FAIRE MAINTENANT ? <strong>3&nbsp;ÉTAPES FACILES&nbsp;:</strong></h2>
      </div>
    </div>
    <div id="step-container" class="container">
      <div id="step-1" class="step">
        <h3>1</h3>
        <p class="step-txt">Si vous êtes le propriétaire, téléversez (upload) vos fichiers dans le répertoire public_html</p>
        <a href="http://<?php echo $_SERVER['SERVER_NAME'];?>/cpanel"><img src="//cdn.planethoster.net/welcome/dossier_cp.png" class="step-img-logo" /></a>
        <a href="http://<?php echo $_SERVER['SERVER_NAME'];?>/cpanel" class="step-cta">CONNEXION CPANEL</a>
      </div>
      <div id="step-2" class="step">
        <h3>2</h3>
        <p class="step-txt">Pour plus d’informations, consultez la base des connaissances</p>
        <a href="https://my.planethoster.net/knowledgebase" target="_blank"><img src="//cdn.planethoster.net/welcome/livrs_cp.png" class="step-img-logo" /></a>
        <a href="https://my.planethoster.net/knowledgebase" target="_blank" class="step-cta">VOIR BASE DE CONNAISSANCE</a>
      </div>
      <div id="step-3" class="step">
        <h3>3</h3>
        <p class="step-txt">Visionnez cette vidéo pour vous assister à la connexion au </p>
        <a href="https://my.planethoster.net/tutorial-video?id=sI7l8RGbDgk" target="_blank"><img src="//cdn.planethoster.net/welcome/ordi_pg.png" class="step-img-logo" /></a>
        <a href="https://my.planethoster.net/tutorial-video?id=sI7l8RGbDgk" target="_blank" class="step-cta">VISIONNER LA VIDÉO</a>
      </div>
    </div>
  </div>
  <footer>
    <div class="container">
      <div class="section section-legal"><a href="https://www.planethoster.net/fr/Termes-Utilisation">Termes d'utilisation</a> | <a href="https://www.planethoster.net/fr/Politique-Vie-Privee">Politique et vie privée</a></div>
      <div class="section section-copyright">Copyright © <?php echo date("Y"); ?> PlanetHoster Inc. Tous Droits Réservés</div>
      <div class="section section-social">
        <span>Restez en contact avec PlanetHoster&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <a href="https://www.facebook.com/PlanetHoster" class="ph-social-4icons ph-social-facebook" target="_blank"></a>
        <a href="https://twitter.com/planethoster" class="ph-social-4icons ph-social-twitter"></a>
        <a href="https://plus.google.com/104430898324979608625/about" class="ph-social-4icons ph-social-googlep"></a>
        <a href="https://www.linkedin.com/company/planethoster" class="ph-social-4icons ph-social-linkedin"></a>
      </div>     
    </div>
  </footer>
</div>
</body>
</html>