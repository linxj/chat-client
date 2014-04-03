<?php
class ChatroomsController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $this->set('chatrooms', $this->Chatroom->find('all'));
		$this->set('username', $this->Auth->user('username'));
    }
	
	public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid chatroom'));
        }

        $chatroom = $this->Chatroom->findById($id);
        if (!$chatroom) {
            throw new NotFoundException(__('Invalid chatroom'));
        }
        $this->set('chatroom', $chatroom);
    }
	
	public function add() {
        if ($this->request->is('post')) {
            $this->Chatroom->create();
            if ($this->Chatroom->save($this->request->data)) {
                $this->Session->setFlash(__('Your chatroom has been saved.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add your chatroom.'));
        }
    }
	
	public function edit($id = null) {
		if (!$id) {
			throw new NotFoundException(__('Invalid chatroom'));
		}

		$chatroom = $this->Chatroom->findById($id);
		if (!$chatroom) {
			throw new NotFoundException(__('Invalid chatroom'));
		}

		if ($this->request->is(array('post', 'put'))) {
			$this->Chatroom->id = $id;
			if ($this->Chatroom->save($this->request->data)) {
				$this->Session->setFlash(__('Your chatroom has been updated.'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Session->setFlash(__('Unable to update your chatroom.'));
		}

		if (!$this->request->data) {
			$this->request->data = $chatroom;
		}
	}
	
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Chatroom->delete($id)) {
			$this->Session->setFlash(
				__('The chatroom with id: %s has been deleted.', h($id))
			);
			return $this->redirect(array('action' => 'index'));
		}
	}
}
?>