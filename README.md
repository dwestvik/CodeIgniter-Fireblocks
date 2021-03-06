# CodeIgniter Fireblocks

Block based layout system for CodeIgniter

The layout module provides a block based rendering system for Codeigniter.
It uses a IOC type of configuration via XML that "wires up" block and child blocks. 
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

#### Notice

I'm sure there are areas in the code that are not ready for public sites.
This was originally written to host internal sites where trusted people controlled the content.
Hooks are put in block rending for future output filtering.
Look at the Block_abstract rendering (_toHTML) and make sure it's cleaning rendered output to your needs.

Remember, PHP renders everything as one big script. So as blocks are rendered, data attributes can conflict between blocks if you are not careful.
If you start seeing 'strange' stuff in your output, look at the property names you are using. Using the $B->getVarname() will work
better than the $B->varname syntax. 
