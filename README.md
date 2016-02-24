# php-orm
This project was initially created for my personal needs. I needed a simple Object Relationship Mapping system, to generate classes for my database tables and use them in simple php projects.

The main class which does all the work is dbORM.class.php, it will generate the properties(columns) and the basic CRUD methods. It will also generate additional methods like
* GetAll
* Find ($where)
* get_by_pk_colum_name($c1,$c2,..)
* get_by_unique($c1,$c2,..)
* get_by_index_column_name($c1)
* Aggregate($aggregateFieldFormula = sum(amount), $wherePart)

Feel free to contact me(besmiralia@gmail.com) for additional info
