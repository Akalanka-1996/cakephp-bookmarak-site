<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class BookmarksController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Validate');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        // $this->Flash->default('default');
        // $this->Flash->error('error');
        //$this->viewBuilder()->layout('ajax');
        $this->paginate = [
            'contain' => ['Users', 'Tags']
        ];
        $bookmarks = $this->paginate($this->Bookmarks);

        $this->set(compact('bookmarks'));
        $this->set('_serialize', ['bookmarks']);
    }

    // get the bookmark collection for exporting

    /*
    public function export($limit = 100) {
        $limit = $this->Validate-> validLimit($limit, 100);
        $bookmarks = $this->Bookmarks->find('all')->limit($limit) // find all the bookmarks
            ->where(['user_id' => 1])
            // ->contain(['Tags']);    // contain takes array of associte tables to include
            // anonymous funtion
            ->contain(['Tags' => function ($q) {
                return $q->where(['Tags.name LIKE' => '%t%']);  // grab tags with letter t
            }]);
        $this->set('bookmarks', $bookmarks);    // view layer now have access to the bookmarks
    }
    */

    public function export($limit = 100) {
        $limit = $this->Validate->validLimit($limit, 100);
        $bookmarks = $this->Bookmarks
            ->find('forUser', ['user_id' => 1])
            ->limit($limit);
        $this->set('_serialize', 'bookmarks');
        $this->set('_header', ['Title', 'URL']);
        $this->set('_extract', ['title', 'url']);
        $this->viewBuilder()->className('CsvViwe.Csv');
        $this->set('bookmarks', $bookmarks);
    }

        public function collectionTest()
    {
        $this->autoRender = false;

        $bookmarks = $this->Bookmarks
            ->find('list');

        debug("Each");
        $bookmarks->each(function ($value, $key) {
            echo "Element $key: $value";
        });

        $bookmarks = $this->Bookmarks
            ->find('all')
            ->contain([
                'Users', 'Tags',
            ]);

        $collection = $bookmarks->extract('title');
        debug("Extract:title");
        debug($collection);
        debug($collection->toArray());

        $collection = $bookmarks->extract(function ($bookmark) {
            return $bookmark->user->id . ', ' . $bookmark->user->name;
        });
        debug("Extract:callback");
        debug($collection);
        debug($collection->toArray());

        $collection = $bookmarks->filter(function ($bookmark, $key) {
            return $bookmark->user->id === 1;
        });
        debug("Filter:callback");
        debug($collection);
        debug($collection->toArray());

        $collection = $bookmarks->reject(function ($bookmark, $key) {
            return $bookmark->user->id === 1;
        });
        debug("Reject:callback");
        debug($collection);
        debug($collection->toArray());

        $boolResult = $bookmarks->every(function ($bookmark, $key) {
            return $bookmark->user->id === 1;
        });
        debug("Every:callback");
        debug($boolResult);

        $boolResult = $bookmarks->some(function ($bookmark, $key) {
            return $bookmark->user->id === 1;
        });
        debug("Some:callback");
        debug($boolResult);

        $minResult = $bookmarks->min(function ($bookmark) {
            return count($bookmark->tags);
        });
        debug("Min:callback");
        debug($minResult);

        $maxResult = $bookmarks->max(function ($bookmark) {
            return count($bookmark->tags);
        });
        debug("Max:callback");
        debug($maxResult);

        $countByResult = $bookmarks->countBy(function ($bookmark) {
            return (count($bookmark->tags) == 1) ? 'One Tag' : 'More Than One Tag';
        });
        debug("CountBy:callback");
        debug($countByResult);
        debug($countByResult->toArray());
    }

    /**
     * View method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Users', 'Tags']
        ]);

        $this->set('bookmark', $bookmark);
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bookmark = $this->Bookmarks->newEntity();
        if ($this->request->is('post')) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->data);
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('The bookmark has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The bookmark could not be saved. Please, try again.'));
            }
        }
        $users = $this->Bookmarks->Users->find('list', ['limit' => 200]);
        $tags = $this->Bookmarks->Tags->find('list', ['limit' => 200]);
        $this->set(compact('bookmark', 'users', 'tags'));
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Tags']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->data);
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('The bookmark has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The bookmark could not be saved. Please, try again.'));
            }
        }
        $users = $this->Bookmarks->Users->find('list', ['limit' => 200]);
        $tags = $this->Bookmarks->Tags->find('list', ['limit' => 200]);
        $this->set(compact('bookmark', 'users', 'tags'));
        $this->set('_serialize', ['bookmark']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);
        if ($this->Bookmarks->delete($bookmark)) {
            $this->Flash->success(__('The bookmark has been deleted.'));
        } else {
            $this->Flash->error(__('The bookmark could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
