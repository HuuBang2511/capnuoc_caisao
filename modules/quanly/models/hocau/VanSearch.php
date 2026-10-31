<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Van;

/**
 * VanSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Van`.
 */
class VanSearch extends Van
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid_1', 'objectid', 'covan', 'cochiakhoa', 'sovong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaivan_id'], 'integer'],
            [['geom', 'tinh_trang', 'mavan', 'vitri', 'loaivan', 'chieudong', 'dongmo', 'ngaylapdat', 'ghichu', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten'], 'safe'],
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
        $query = Van::find()->where(['status' => 1]);

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
            'covan' => $this->covan,
            'cochiakhoa' => $this->cochiakhoa,
            'sovong' => $this->sovong,
            'ngaylapdat' => $this->ngaylapdat,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'tinhtrang_id' => $this->tinhtrang_id,
            'loaivan_id' => $this->loaivan_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(mavan)', mb_strtoupper($this->mavan)])
            ->andFilterWhere(['like', 'upper(vitri)', mb_strtoupper($this->vitri)])
            ->andFilterWhere(['like', 'upper(loaivan)', mb_strtoupper($this->loaivan)])
            ->andFilterWhere(['like', 'upper(chieudong)', mb_strtoupper($this->chieudong)])
            ->andFilterWhere(['like', 'upper(dongmo)', mb_strtoupper($this->dongmo)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
            ->andFilterWhere(['like', 'upper(geojson)', mb_strtoupper($this->geojson)])
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
        'mavan',
        'vitri',
        'covan',
        'loaivan',
        'cochiakhoa',
        'sovong',
        'chieudong',
        'dongmo',
        'ngaylapdat',
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
        'loaivan_id',        ];
    }
}
