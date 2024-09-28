<style>

    
    .page-content {
        padding-right:5px;
        padding-left:5px;      
    }
    .button {
        margin-right: auto;
        margin-left:auto;
        max-width:200px;
        color:red;
    }
    
    @media only screen and (min-width:750px){
        .page-content{
            background-image : url('images/fondecran.png');
            background-repeat: no-repeat;
            background-position:center center;
        }
    }
    
      .demo-card-header-pic .card-header {
        height: 20px;
        background-size: cover;
        background-position: center;
      }

      .card {
        background-image: url('images/accueil/background.jpg');
          max-width:600px; 
          margin-left: auto;
          margin-right: auto;
      }

      @font-face {
          font-family: 'magmawave_capsbold';
          src: url('css/fonts/magmawave_caps-webfont.eot');
          src: url('css/fonts/magmawave_caps-webfont.eot?#iefix') format('embedded-opentype'),
               url('css/fonts/magmawave_caps-webfont.woff2') format('woff2'),
               url('css/fonts/magmawave_caps-webfont.woff') format('woff'),
               url('css/fonts/magmawave_caps-webfont.ttf') format('truetype'),
               url('css/fonts/magmawave_caps-webfont.svg#magmawave_capsbold') format('svg');
          font-weight: normal;
          font-style: normal;

      }

      @font-face {
          font-family: 'bpmoleculesregular';
          src: url('css/fonts/bpmolecules-webfont.eot');
          src: url('css/fonts/bpmolecules-webfont.eot?#iefix') format('embedded-opentype'),
               url('css/fonts/bpmolecules-webfont.woff2') format('woff2'),
               url('css/fonts/bpmolecules-webfont.woff') format('woff'),
               url('css/fonts/bpmolecules-webfont.ttf') format('truetype'),
               url('css/fonts/bpmolecules-webfont.svg#bpmoleculesregular') format('svg');
          font-weight: normal;
          font-style: normal;

      }

      .magma {
        font-family: magmawave_capsbold;
      }

      .atome {
        font-family: bpmoleculesregular;
        font-weight:bold;
        font-size:20px;
      }

      .imageAtome {
        float:left;
        width:70px;
        height:70px;
      }
    
    .card-header{
        font-family: magmawave_capsbold;
        color:white;
        background-color: black;
    }
    
    hr { 
      border: 0; 
      height: 1px; 
      background-image: -webkit-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
      background-image: -moz-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
      background-image: -ms-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
      background-image: -o-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0); 
    }
    
    .lien {
        font-weight: bold;
    }
    
    .lienFormule {
        -webkit-box-shadow: 0px 2px 0px gray;
        -moz-box-shadow: 0px 2px 0px gray;
        box-shadow: 0px 2px 0px gray;
        background-color: #D9D9D9;
        padding: 5px 5px 5px 5px;
        border-radius: 4px;
    }
    
    .important {
        font-weight:bold;
        color:black;
        font-variant: small-caps;
    }
    
    .subimportant {
        font-weight:bold;
    }
    
    table {
        border-collapse: collapse;
        width: 94%;
        margin-right:3%;
        margin-left:3%;
        text-align:center;
    }
    
    .table-responsive {
        overflow-x:auto;
    }

    td {
        padding: 8px;
        border-top: 1px solid gray;
    }
    
    th {
        padding: 8px;
    }
    
    .action {
        margin-right:25px;
    }
    
    
    .toolbar {
        box-shadow: 8px 8px 0px #aaa;
    }
    
    .facebook-card .card-header {
        display: block;
        padding: 10px;
        font-family: default;
        color:black;
        background-color: white;
    }
    .facebook-card .facebook-avatar {
        float: left;
        font-size: 10px;
        margin-bottom:10px;
    }
    .facebook-card .facebook-grade {
        float: right;
        font-size: 10px;
        margin-bottom:10px;
        margin-top : 10px;
    }  
    .facebook-card .facebook-name {
        margin-left: 80px;
        margin-top:10px;
        font-size: 17px;
        font-weight: 500;
    }
    .facebook-card .facebook-date {
        margin-left: 80px;
        font-size: 10px;
        color: #8e8e93;
        margin-top:-10px;
    }
    .facebook-card .card-footer {
        background: #fafafa;
    }
    .facebook-card .card-footer a {
        color: #81848b;
        font-weight: 500;
    }
    .facebook-card .card-content img {
        display: block;
    }
    .facebook-card .card-content-inner {
        padding: 15px 10px;
    }  
    
    .partie {
       font-weight:bold;
        color:red;
        font-variant: small-caps; 
    }
    
    .sousPartie {
       font-weight:bold;
        color:black;
        font-variant: small-caps; 
    }
    
    .sousSousPartie {
       font-weight:bold;
        color:gray;
        font-variant: small-caps; 
    }
    
    .image-centree {
	    text-align: center;
	    font-weight:bold;
    }
    
    .lienSousMenu {
        color:white;
        text-align:center;
        margin-right:10px;
        margin-left:10px;
    }
    
    .lienVisible {
        border-bottom: 1px dashed black;
    }
    
    .imageSousMenu {
        margin-top:3px;
        height:32px;
        width:32px;
    }
    
    .labelSousMenu {
        color:white;
    }
    
    .labelClassement{
        color:black;
        font-size: 12px;
        font-weight: 500;
    }
    
    .imageAide {
        width:20px;
        height:20px;
        vertical-align:middle;
    }
    
    .imageAide2 {
        width:30px;
        height:30px;
        vertical-align:-40%;
    }
    
    .imageChip {
        width: 25px;
        height: 25px;
        border-radius: 0px;
    }
    
    .imageClassement{
        width: 32px;
        height; 32px;
    }

    .titreAide {
        background-color:black;
        width:100%;
        font-size: 17px;
        height:35px;
        font-family: magmawave_capsbold;
        font-weight: bold;
        text-align: center;
        color:white;
        display: block;
        margin: 0px 0px 0px 0px;
    }
    
    .align {
        vertical-align: middle;
    }
    
    .toolbarcustom {
        box-shadow: 5px 2px 5px 5px rgba(0, 0, 0, 0.2);
        height:65px;
    }
    
    .imageMedia {
        width:50px;
        height:50px;
    }
    
    .iconeMenu {
        width:25px;
        height:25px;
    }
    
    .w32 {
        width: 32px;
        height: 32px;
    }
    
    .w16 {
        width: 16px;
        height: 16px;
    }
</style>