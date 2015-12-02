var _giLoadScript = function(url, callback) {
   var head = document.getElementsByTagName('head')[0];
   var script = document.createElement('script');
   script.type = 'text/javascript';
   script.src = url;

   script.onreadystatechange = callback;
   script.onload = callback;
   head.appendChild(script);
};

var _giRun = function() {

   window.GIAPI = {
      checkout: function(opts, cb) {
         Stripe.setPublishableKey(opts.key);

         Stripe.card.createToken({
             number:       opts.card,
             cvc:          opts.cvc,
             exp_month:    opts.month,
             exp_year:     opts.year
         }, function(status, resp) {
            if( resp.error ) {
               cb(false);
            } else {
               var tok = resp['id'];
               cb(tok);
            }
         });
      }
   };


};

_giLoadScript('https://js.stripe.com/v2/', _giRun);
