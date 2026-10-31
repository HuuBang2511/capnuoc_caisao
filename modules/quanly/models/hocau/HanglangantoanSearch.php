<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Hanglangantoan;

/**
 * HanglangantoanSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Hanglangantoan`.
 */
class HanglangantoanSearch extends Hanglangantoan
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id'], 'integer'],
            [['geom', 'hanhlang', 'tinh_trang', 'ghichu', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten', 'ma'], 'safe'],
            [['shape_leng'], 'number'],
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
        $query = Hanglangantoan::find()->where(['status' => 1]);

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'tinhtrang_id' => $this->tinhtrang_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(hanhlang)', mb_strtoupper($this->hanhlang)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
            ->andFilterWhere(['like', 'upper(geojson)', mb_strtoupper($this->geojson)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
            ->andFilterWhere(['like', 'upper(ma)', mb_strtoupper($this->ma)])
            ->andFilterWhere(['like', 'upper(file_dinhkem)', mb_strtoupper($this->file_dinhkem)]);

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
        'hanhlang',
        'tinh_trang',
        'ghichu',
        'shape_leng',
        'lat',
        'long',
        'geojson',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'file_dinhkem',
        'tinhtrang_id',        ];
    }
}
