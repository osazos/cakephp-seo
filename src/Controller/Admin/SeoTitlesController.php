<?php
namespace Seo\Controller\Admin;

use Seo\Controller\Admin\AppController;

/**
 * SeoTitles Controller
 *
 * @property \Seo\Model\Table\SeoTitlesTable $SeoTitles
 */
class SeoTitlesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['SeoUris']
        ];
        $this->set('seoTitles', $this->paginate($this->SeoTitles));
        $this->set('_serialize', ['seoTitles']);
    }

    /**
     * View method
     *
     * @param string|null $id Seo Title id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $seoTitle = $this->SeoTitles->get($id, [
            'contain' => ['SeoUris']
        ]);
        $this->set('seoTitle', $seoTitle);
        $this->set('_serialize', ['seoTitle']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $seoTitle = $this->SeoTitles->newEntity();
        if ($this->request->is('post')) {
            $seoTitle = $this->SeoTitles->patchEntity($seoTitle, $this->request->data);
            if ($this->SeoTitles->save($seoTitle)) {
                $this->Flash->success(__('The seo title has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The seo title could not be saved. Please, try again.'));
            }
        }
        $seoUris = $this->SeoTitles->SeoUris->find('list', ['limit' => 200]);
        $this->set(compact('seoTitle', 'seoUris'));
        $this->set('_serialize', ['seoTitle']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Seo Title id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $seoTitle = $this->SeoTitles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $seoTitle = $this->SeoTitles->patchEntity($seoTitle, $this->request->data);
            if ($this->SeoTitles->save($seoTitle)) {
                $this->Flash->success(__('The seo title has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The seo title could not be saved. Please, try again.'));
            }
        }
        $seoUris = $this->SeoTitles->SeoUris->find('list', ['limit' => 200]);
        $this->set(compact('seoTitle', 'seoUris'));
        $this->set('_serialize', ['seoTitle']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Seo Title id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $seoTitle = $this->SeoTitles->get($id);
        if ($this->SeoTitles->delete($seoTitle)) {
            $this->Flash->success(__('The seo title has been deleted.'));
        } else {
            $this->Flash->error(__('The seo title could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
