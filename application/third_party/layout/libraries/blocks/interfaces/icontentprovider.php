<?php
/*
 * Content Provider Interface
 * Used for blocks that request content from a model.
 */

/**
 *
 * @author dane.westvik
 */
interface iContentProvider {
    public function getContent($contentName);
}
