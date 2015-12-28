<?php
namespace Seo\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;
use Seo\Model\Entity\SeoUri;

/**
 * SeoUris Model
 *
 * @property \Cake\ORM\Association\HasMany $SeoCanonicals
 * @property \Cake\ORM\Association\HasMany $SeoMetaTags
 * @property \Cake\ORM\Association\HasMany $SeoTitles
 */
class SeoUrisTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('seo_uris');
        $this->displayField('uri');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->addBehavior('Search.Search');

        $this->hasMany('SeoMetaTags', [
            'foreignKey' => 'seo_uri_id',
            'className' => 'Seo.SeoMetaTags',
            'dependent' => true
        ]);
        
        $this->hasOne('SeoCanonicals', [
            'foreignKey' => 'seo_uri_id',
            'className' => 'Seo.SeoCanonicals',
            'dependent' => true
        ]);
        
        $this->hasOne('SeoTitles', [
            'foreignKey' => 'seo_uri_id',
            'className' => 'Seo.SeoTitles',
            'dependent' => true
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('uri', 'create')
            ->notEmpty('uri')
            ->add('uri', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->add('is_approved', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('is_approved');

        return $validator;
    }

    /**
     * Find Approved
     *
     * @param Cake\ORM\Query $query A query object
     * @param array $options Options
     * @return Cake\ORM\Query
     */
    public function findApproved(Query $query, array $options)
    {
        $query->where(['SeoUris.is_approved' => true])
            ->contain([
                'SeoTitles',
                'SeoCanonicals',
                'SeoMetaTags'
            ]);
        return $query;
    }

    /**
     * After Save Callback
     *
     * @param Cake/Event/Event $event The afterSave event that was fired.
     * @param Cake/ORM/Entity $entity The entity
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterSave(\Cake\Event\Event $event, \Cake\ORM\Entity $entity, $options)
    {
        Cache::clear(false, 'seo');
    }

    /**
     * Get By Uri
     *
     * Find and return a full SeoUri Entity
     *
     * @param string $uri the Uri to find
     * @param bool $approved filter on approved flag
     * @return \Cake\ORM\Entity
     */
    public function getByUri($uri, $approved = true)
    {
        return Cache::remember('uri_' . md5($uri), function () use ($uri, $approved) {
            $data = $this->findByUri($uri)->contain([
                'SeoTitles',
                'SeoCanonicals',
                'SeoMetaTags'
            ]);

            if ($approved) {
                $data->find('approved');
            }
            return $data->first();
        }, 'seo');
    }

    /**
     * Search Configuration used for FriendOfCake/search plugin
     * @see https://github.com/FriendsOfCake/search
     * @return Search object
     */
    public function searchConfiguration()
    {
        $search = new Manager($this);
        $search
            ->like('uri', [
                'before' => true,
                'after' => true,
                'field' => [$this->aliasField('uri')]
            ])
            ->like('before-uri', [
                'before' => true,
                'after' => false,
                'field' => [$this->aliasField('uri')]
            ])
            ->like('after-uri', [
                'before' => false,
                'after' => true,
                'field' => [$this->aliasField('uri')]
            ]);
        return $search;
    }
}
