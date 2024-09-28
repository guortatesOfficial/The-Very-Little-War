var app = {

    registrationId : "",
    // Application Constructor
    initialize: function() {
        document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
    },

    // deviceready Event Handler
    //
    // Bind any cordova events here. Common events are:
    // 'pause', 'resume', etc.
    onDeviceReady: function() {
        this.receivedEvent('deviceready');
    },

    // Update DOM on a Received Event
    receivedEvent: function(id){
    
    var pushNotification = window.plugins.pushNotification;
    pushNotification.register(app.successHandler, app.errorHandler,{"senderID":"1086347055283","ecb":"app.onNotificationGCM"});
    },
  
  successHandler: function(result) {
  },
  
  errorHandler:function(error) {
    alert(error);
  },
  
  onNotificationGCM: function(e) {
        switch( e.event )
        {
            case 'registered':
                if ( e.regid.length > 0 )
                {
                    this.registrationId = e.regid; // on a l'id du GCM
          
                    function ajaxGet(url, callback) {
                       var req = new XMLHttpRequest();
                      req.open("GET", url);
                      req.addEventListener("load", function () {
                        if (req.status >= 200 && req.status < 400) {
                          // Appelle la fonction callback en lui passant la réponse de la requête
                          callback(req.responseText);
                        } else {
                          console.error(req.status + " " + req.statusText + " " + url);
                        }
                      });
                      req.addEventListener("error", function () {
                        console.error("Erreur réseau avec l'URL " + url);
                      });
                      req.send(null);
                    }

                    ajaxGet("http://www.theverylittlewar.com/tests/inscrireCle.php?cle="+e.regid,function(){
                    })
          
                }
            break;
 
            case 'message':
              // this is the actual push notification. its format depends on the data model from the push server
            break;
 
            case 'error':
              alert('GCM error = '+e.msg);
            break;
 
            default:
              alert('An unknown GCM event has occurred');
              break;
        }
    }
};

app.initialize();