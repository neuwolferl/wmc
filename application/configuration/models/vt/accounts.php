<?php

namespace VT {

    class Accounts extends VtModule {

        protected static $_moduleName = "Accounts";
        
        protected static $_primary_key = 'id';

        protected static $_vm;

        protected static $has_one;

        protected static $has_many;

        protected static $alias_attribute;
        
        protected static $_idPrefix = '11';

    }

}
?>