<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Ongphanphoi;

/**
 * OngphanphoiSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Ongphanphoi`.
 */
class OngphanphoiSearch extends Ongphanphoi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid_1', 'objectid', 'coong', 'status', 'created_by', 'updated_by', 'tinhtrang_id', 'loaiong_id'], 'integer'],
            [['geom', 'tinh_trang', 'vatlieu', 'mavattu', 'ngaylapdat', 'congtrinh', 'dvtk', 'dvtc', 'bvhc', 'ghichu', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten', 'ma'], 'safe'],
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
        $query = Ongphanphoi::find()->where(['status' => 1]);

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
            'coong' => $this->coong,
            'ngaylapdat' => $this->ngaylapdat,
            'shape_leng' => $this->shape_leng,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'tinhtrang_id' => $this->tinhtrang_id,
            'loaiong_id' => $this->loaiong_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(vatlieu)', mb_strtoupper($this->vatlieu)])
            ->andFilterWhere(['like', 'upper(mavattu)', mb_strtoupper($this->mavattu)])
            ->andFilterWhere(['like', 'upper(congtrinh)', mb_strtoupper($this->congtrinh)])
            ->andFilterWhere(['like', 'upper(dvtk)', mb_strtoupper($this->dvtk)])
            ->andFilterWhere(['like', 'upper(dvtc)', mb_strtoupper($this->dvtc)])
            ->andFilterWhere(['like', 'upper(bvhc)', mb_strtoupper($this->bvhc)])
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
        'vatlieu',
        'coong',
        'mavattu',
        'ngaylapdat',
        'congtrinh',
        'dvtk',
        'dvtc',
        'bvhc',
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
        'tinhtrang_id',
        'loaiong_id',        ];
    }
}
