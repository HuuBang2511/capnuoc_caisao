<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Ongdansinh;

/**
 * OngdansinhSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Ongdansinh`.
 */
class OngdansinhSearch extends Ongdansinh
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id1', 'status', 'created_by', 'updated_by', 'coong', 'tinhtrang_id'], 'integer'],
            [['geom', 'created_at', 'updated_at', 'lat', 'long', 'geojson', 'file_dinhkem', 'vatlieu', 'ten', 'ma'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Ongdansinh::find()->where(['status' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id1' => $this->id1,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'coong' => $this->coong,
            'tinhtrang_id' => $this->tinhtrang_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
            ->andFilterWhere(['like', 'upper(geojson)', mb_strtoupper($this->geojson)])
            ->andFilterWhere(['like', 'upper(file_dinhkem)', mb_strtoupper($this->file_dinhkem)])
            ->andFilterWhere(['like', 'upper(ma)', mb_strtoupper($this->ma)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
            ->andFilterWhere(['like', 'upper(vatlieu)', mb_strtoupper($this->vatlieu)]);

        return $dataProvider;
    }

    public function getExportColumns()
    {
        return [
            [
                'class' => 'kartik\grid\SerialColumn',
            ],
            'id',
        'geom',
        'id1',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'lat',
        'long',
        'geojson',
        'file_dinhkem',
        'vatlieu',
        'coong',
        'tinhtrang_id',        ];
    }
}
