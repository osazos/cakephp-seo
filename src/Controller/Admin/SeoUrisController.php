<?php
namespace Seo\Controller\Admin;

use Seo\Controller\Admin\AppController;

/**
 * SeoUris Controller
 *
 * @property \Seo\Model\Table\SeoUrisTable $SeoUris
 */
class SeoUrisController extends AppController
{

    /**
     * Initialize
     */
    public function initialize()
    {
        parent::initialize();
        if ($this->request->action === 'index') {
            $this->loadComponent('Search.Prg');
        }
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $query = $this->SeoUris
            ->find('search', $this->SeoUris->filterParams($this->request->query))
            ->order(['SeoUris.uri' => 'asc']);

        $this->set('seoUris', $this->paginate($query));
        $this->set('_serialize', ['seoUris']);
    }

    /**
     * View method
     *
     * @param string|null $id Seo Uri id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $seoUri = $this->SeoUris->get($id, [
            'contain' => ['SeoCanonicals', 'SeoMetaTags', 'SeoTitles']
        ]);
        $this->set('seoUri', $seoUri);
        $this->set('_serialize', ['seoUri']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $seoUri = $this->SeoUris->newEntity();
        if ($this->request->is('post')) {
            $seoUri = $this->SeoUris->patchEntity($seoUri, $this->request->data);
            if ($this->SeoUris->save($seoUri)) {
                $this->Flash->success(__('The seo uri has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The seo uri could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('seoUri'));
        $this->set('_serialize', ['seoUri']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Seo Uri id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $seoUri = $this->SeoUris->get($id, [
            'contain' => [
                'SeoTitles',
                'SeoMetaTags',
                'SeoCanonicals'
            ]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $seoUri = $this->SeoUris->patchEntity($seoUri, $this->request->data);
            if ($this->SeoUris->save($seoUri)) {
                $this->Flash->success(__('The seo uri has been saved.'));
                return $this->redirect($this->referer());
            } else {
                $this->Flash->error(__('The seo uri could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('seoUri'));
        $this->set('_serialize', ['seoUri']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Seo Uri id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $seoUri = $this->SeoUris->get($id);
        if ($this->SeoUris->delete($seoUri)) {
            $this->Flash->success(__('The seo uri has been deleted.'));
        } else {
            $this->Flash->error(__('The seo uri could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
