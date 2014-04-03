<h1>Add Chatroom</h1>
<?php
echo $this->Form->create('Chatroom');
echo $this->Form->input('title');
echo $this->Form->input('description', array('rows' => '3'));
echo $this->Form->end('Save Post');
?>