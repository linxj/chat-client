<h1>Edit Chatroom</h1>
<?php
echo $this->Form->create('Chatroom');
echo $this->Form->input('title');
echo $this->Form->input('description', array('rows' => '3'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save Post');
?>