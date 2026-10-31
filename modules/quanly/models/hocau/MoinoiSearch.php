<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Moinoi;

/**
 * MoinoiSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Moinoi`.
 */
class MoinoiSearch extends Moinoi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaimoinoi_id'], 'integer'],
            [['geom', 'tinh_trang', 'loaimoinoi', 'kichthuoc', 'vattu', 'mavitri', 'ghichu', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten', 'ma'], 'safe'],
            [['x', 'y', 'z'], 'number'],
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
        $query = Moinoi::find()->where(['status' => 1]);

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
            'objectid_1' => $this->objectid_1,
            'objectid' => $this->objectid,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'tinhtrang_id' => $this->tinhtrang_id,
            'loaimoinoi_id' => $this->loaimoinoi_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(loaimoinoi)', mb_strtoupper($this->loaimoinoi)])
            ->andFilterWhere(['like', 'upper(kichthuoc)', mb_strtoupper($this->kichthuoc)])
            ->andFilterWhere(['like', 'upper(vattu)', mb_strtoupper($this->vattu)])
            ->andFilterWhere(['like', 'upper(mavitri)', mb_strtoupper($this->mavitri)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
            ->andFilterWhere(['like', 'upper(geojson)', mb_strtoupper($this->geojson)])
            ->andFilterWhere(['like', 'upper(ma)', mb_strtoupper($this->ma)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
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
        'objectid_1',
        'objectid',
        'tinh_trang',
        'loaimoinoi',
        'kichthuoc',
        'x',
        'y',
        'z',
        'vattu',
        'mavitri',
        'ghichu',
        'lat',
        'long',
        'geojson',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'file_dinhkem',
        'tinhtrang_id',
        'loaimoinoi_id',        ];
    }
}
