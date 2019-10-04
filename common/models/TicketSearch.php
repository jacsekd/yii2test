<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Ticket;

/**
 * TicketSearch represents the model behind the search form of `\common\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    /**
     * {@inheritdoc}
     */



    public function rules()
    {
        return [
            [['updated_at', 'id', 'status', 'author_id', 'created_at', 'last_comment_time', 'admin_id'], 'integer'],
            [['text', 'title', 'secret_id'], 'safe'],
            [['author.username'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['author.username']);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $query = Ticket::find()
            ->leftJoin("user", "ticket.author_id=user.id")
            ->leftJoin("usernames", "admin_id=usernames.id")
            ->andWhere(['or',
                ['user.status' => User::STATUS_ACTIVE],
                ['user.status' => User::STATUS_INACTIVE]
            ])
            ->andWhere(['not', ['ticket.id' => null]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
            'sort' => ['attributes' => ['title']],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere(['LIKE', 'user.username', $this->getAttribute('author.username')])
        ->andFilterWhere(['LIKE', 'title', $this->title]);

        $query->addOrderBy(['user.username' => SORT_ASC])
        ->addOrderBy(['ticket.status' => SORT_DESC]);
        return $dataProvider;
    }
}
