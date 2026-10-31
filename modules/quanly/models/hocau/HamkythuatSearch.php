<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Hamkythuat;

/**
 * HamkythuatSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Hamkythuat`.
 */
class HamkythuatSearch extends Hamkythuat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid_1', 'objectid', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiham_id'], 'integer'],
            [['geom', 'tinh_trang', 'maham', 'loai_ham', 'kichthuoc', 'vatlieu', 'sonap', 'vitri', 'ngaylapdat', 'dvtk', 'dvtc', 'bvhc', 'ghichu', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten'], 'safe'],
            [['shape_leng', 'shape_area'], 'number'],
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
        $query = Hamkythuat::find()->where(['status' => 1]);

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
            'ngaylapdat' => $this->ngaylapdat,
            'shape_leng' => $this->shape_leng,
            'shape_area' => $this->shape_area,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'tinhtrang_id' => $this->tinhtrang_id,
            'loaiham_id' => $this->loaiham_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(maham)', mb_strtoupper($this->maham)])
            ->andFilterWhere(['like', 'upper(loai_ham)', mb_strtoupper($this->loai_ham)])
            ->andFilterWhere(['like', 'upper(kichthuoc)', mb_strtoupper($this->kichthuoc)])
            ->andFilterWhere(['like', 'upper(vatlieu)', mb_strtoupper($this->vatlieu)])
            ->andFilterWhere(['like', 'upper(sonap)', mb_strtoupper($this->sonap)])
            ->andFilterWhere(['like', 'upper(vitri)', mb_strtoupper($this->vitri)])
            ->andFilterWhere(['like', 'upper(dvtk)', mb_strtoupper($this->dvtk)])
            ->andFilterWhere(['like', 'upper(dvtc)', mb_strtoupper($this->dvtc)])
            ->andFilterWhere(['like', 'upper(bvhc)', mb_strtoupper($this->bvhc)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
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
        'maham',
        'loai_ham',
        'kichthuoc',
        'vatlieu',
        'sonap',
        'vitri',
        'ngaylapdat',
        'dvtk',
        'dvtc',
        'bvhc',
        'ghichu',
        'shape_leng',
        'shape_area',
        'geojson',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'file_dinhkem',
        'tinhtrang_id',
        'loaiham_id',        ];
    }
}
