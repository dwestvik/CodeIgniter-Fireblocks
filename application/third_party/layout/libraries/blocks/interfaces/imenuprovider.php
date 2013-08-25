<?php
/*
 * Content Provider Interface
 * Used for blocks that request content from a model.
 */

/**
 *
 * @author dane.westvik
 */
interface iMenuProvider {
    public function getMenuContent($menuName='');
}
