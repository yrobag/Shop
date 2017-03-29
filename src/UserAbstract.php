<?php

namespace Shop;

abstract class UserAbstract extends DbModel
{
    /**
     * Stała określająca ID obiektu nieistniejącego w bazie.
     */
    const NON_EXISTING_ID = -1;
    
    protected $id;
    protected $email;
    protected $name;
    protected $pass;

    /** @ToDo: Refactor - klasy User i Admin powinny dziedziczyć po UserAbstract  */
}
