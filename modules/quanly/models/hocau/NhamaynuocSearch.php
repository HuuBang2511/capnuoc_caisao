<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Nhamaynuoc;

/**
 * NhamaynuocSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Nhamaynuoc`.
 */
class NhamaynuocSearch extends Nhamaynuoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'loainhamay_id'], 'integer'],
            [['geom', 'loai', 'file_dinhkem', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'ten', 'ma', 'ghichu'], 'safe'],
            [['objectid', 'shape_leng', 'shape_area'], 'number'],
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
        $query = Nhamaynuoc::find()->where(['status' => 1]);

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
            'objectid' => $this->objectid,
            'shape_leng' => $this->shape_leng,
            'shape_area' => $this->shape_area,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'loainhamay_id' => $this->loainhamay_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(loai)', mb_strtoupper($this->loai)])
            ->andFilterWhere(['like', 'upper(file_dinhkem)', mb_strtoupper($this->file_dinhkem)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
            ->andFilterWhere(['like', 'upper(ma)', mb_strtoupper($this->ma)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
            ->andFilterWhere(['like', 'upper(geojson)', mb_strtoupper($this->geojson)]);

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
        'objectid',
        'loai',
        'shape_leng',
        'shape_area',
        'file_dinhkem',
        'lat',
        'long',
        'geojson',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'loainhamay_id',        ];
    }
}
