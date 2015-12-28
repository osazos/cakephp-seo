<?php
namespace Seo\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Seo\Model\Entity\SeoMetaTag;

/**
 * SeoMetaTags Model
 *
 * @property \Cake\ORM\Association\BelongsTo $SeoUris
 */
class SeoMetaTagsTable extends Table
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

        $this->table('seo_meta_tags');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SeoUris', [
            'foreignKey' => 'seo_uri_id',
            'joinType' => 'INNER',
            'className' => 'Seo.SeoUris'
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
            ->notEmpty('name');

        $validator
            ->allowEmpty('content');

        $validator
            ->add('is_http_equiv', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('is_http_equiv');

        $validator
            ->add('is_property', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('is_property');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['seo_uri_id'], 'SeoUris'));
        return $rules;
    }
}
