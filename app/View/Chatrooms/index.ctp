<h1>Chatrooms</h1>
<div>Hello, <?php echo $username; ?></div>
<div>
<?php echo $this->Html->link('logout',
array('controller' => 'users', 'action' => 'logout')); ?>
</div>
<div>
<?php echo $this->Html->link(
    'Add Chatroom',
    array('controller' => 'chatrooms', 'action' => 'add')
); ?>
</div>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Action</th>
        <th>Created</th>
    </tr>

    <?php foreach ($chatrooms as $chatroom): ?>
    <tr>
        <td><?php echo $chatroom['Chatroom']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($chatroom['Chatroom']['title'],
array('controller' => 'chatrooms', 'action' => 'view', $chatroom['Chatroom']['id'])); ?>
        </td>
		<td>
			<?php
                echo $this->Form->postLink(
                    'Delete',
                    array('action' => 'delete', $chatroom['Chatroom']['id']),
                    array('confirm' => 'Are you sure?')
                );
            ?>
            <?php
                echo $this->Html->link(
                    'Edit',
                    array('action' => 'edit', $chatroom['Chatroom']['id'])
                );
            ?>
        </td>
        <td><?php echo $chatroom['Chatroom']['created']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($chatroom); ?>
</table>