# codeigniter_fireblock

Block based layout system for Codeigniter

The layout module provides a block based rendering system for Codeigniter. 
A layout defines an HTML page and breaks each part of that page down to individual structural and content blocks.
Each block can contain sub-blocks that are used to build a complete page. 

Each block in the layout system consists of a block object and often a template file. 

Each block contains a `$_data` array which contains all values available to the rendered template.

### Structure

Blocks are defined using an XML layout file which defines the parent / child blocks, sets rendering templates, 
bind models to blocks and sets default property values.

Blocks defined in the XML file can also be updated (modified) by sections called "layout updates". 
This lets you define a 'default' layout for all your pages then write smaller updates to this default page as needed.

All blocks extend the `Block_abstract` class which contains many protected hook points to customize the block's operation. 


### Installation

Code contained in third_party/layout. Demo controller and layout/model.

[See wiki for developer guide](https://github.com/dwestvik/CodeIgniter-Fireblocks/wiki)

Update application/config/config.php to include `'http://localhost/fireblock/'`
