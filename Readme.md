# Gaia

  Small and powerful PHP web development framework for professional projects.
  Easy to understand, easy to extend.

     require_once('../../libs/gaia.php');

     gaiaServer::run(
         gaiaServer::router(array(
             '/' => function($req, $res) {
                 $res->send('Hello World!');
             },
             '/admin' => array(
                 gaiaServer::requireBasicAuth(authCallable),
                 loadUser,
                 andRestrictTo('admin'),
                 function($req, $res) {
                     $res->send('Hello Admin!');
                 }
             )
         )),
         function($req, $res) {
             $res->write(gaiaView::render('layout', array('content' => $res->content())));
         }
     );

     function loadUser($req, $res) {
         // fetch user data from db or session
         $req->user = (object) array(
             'name' => 'Foo user',
             'role' => 'user'
         );
     }

     function andRestrictTo($role) {
         return function($req, $res) use ($role) {
             if ($req->user->role !== $role) return gaiaServer::BREAKCHAIN;
         }
     }
