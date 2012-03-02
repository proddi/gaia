# Gaia

  Small and flexible PHP web development framework for professional projects.
  Easy to understand, easy to extend.

     require_once('../../libs/gaia.php');

     gaiaServer::run(
         gaiaServer::router(array(
             '/' => function($req, $res) {
                 $res->send('Hello World!');
             },
             '/admin' => array(
     //            gaiaServer::requireBasicAuth('admin', 'password'),
                 function($req, $res) {
                     $res->send('Hello Admin!');
                 }
             )
         )),
         function($req, $res) {
             $res->write(gaiaView::render('layout', array('content' => $res->content())));
         }
     );