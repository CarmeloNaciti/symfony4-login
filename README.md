# symfony4-login
Basic Symfony 4 Login & User Registration with Materialize UI

![alt tag](https://github.com/CarmeloNaciti/symfony4-login/blob/master/screenshots/login1.png "Login Screen")

![alt tag](https://github.com/CarmeloNaciti/symfony4-login/blob/master/screenshots/login2.png "Registration Screen")

Once this project has been cloned, please run the following commands:
1. Delete the ``screenshots`` folder :)
2. ``composer install`` in ``/``
3. ``yarn install`` in ``/public``
4. Update the ``DATABASE_URL`` parameter in the ``.env`` file with the relevant DB information
5. Create a DB schema and table:
```
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
```
