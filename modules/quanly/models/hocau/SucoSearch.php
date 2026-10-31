<?php

namespace app\modules\quanly\models\hocau;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\hocau\Suco;

/**
 * SucoSearch represents the model behind the search form about `app\modules\quanly\models\hocau\Suco`.
 */
class SucoSearch extends Suco
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'objectid_1', 'objectid', 'masuco', 'status', 'created_by', 'updated_by', 'loaisuco_id', 'nguyennhansuco_id', 'tinhtrangsuco_id', 'tinhtrang_id'], 'integer'],
            [['geom', 'tinh_trang', 'vitri', 'loai', 'n_phathien', 'd_phathien', 'n_xuly', 'd_xuly', 'n_hoancong', 'nguyennhan', 'cachxuly', 'mataisan', 'ghichu', 'lat', 'long', 'geojson', 'created_at', 'updated_at', 'file_dinhkem', 'ten'], 'safe'],
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
        $query = Suco::find()->where(['status' => 1]);

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
            'masuco' => $this->masuco,
            'n_phathien' => $this->n_phathien,
            'n_xuly' => $this->n_xuly,
            'n_hoancong' => $this->n_hoancong,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'loaisuco_id' => $this->loaisuco_id,
            'nguyennhansuco_id' => $this->nguyennhansuco_id,
            'tinhtrangsuco_id' => $this->tinhtrangsuco_id,
            'tinhtrang_id' => $this->tinhtrang_id,
        ]);

        $query->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)])
            ->andFilterWhere(['like', 'upper(tinh_trang)', mb_strtoupper($this->tinh_trang)])
            ->andFilterWhere(['like', 'upper(vitri)', mb_strtoupper($this->vitri)])
            ->andFilterWhere(['like', 'upper(loai)', mb_strtoupper($this->loai)])
            ->andFilterWhere(['like', 'upper(d_phathien)', mb_strtoupper($this->d_phathien)])
            ->andFilterWhere(['like', 'upper(d_xuly)', mb_strtoupper($this->d_xuly)])
            ->andFilterWhere(['like', 'upper(nguyennhan)', mb_strtoupper($this->nguyennhan)])
            ->andFilterWhere(['like', 'upper(cachxuly)', mb_strtoupper($this->cachxuly)])
            ->andFilterWhere(['like', 'upper(mataisan)', mb_strtoupper($this->mataisan)])
            ->andFilterWhere(['like', 'upper(ghichu)', mb_strtoupper($this->ghichu)])
            ->andFilterWhere(['like', 'upper(ten)', mb_strtoupper($this->ten)])
            ->andFilterWhere(['like', 'upper(lat)', mb_strtoupper($this->lat)])
            ->andFilterWhere(['like', 'upper(long)', mb_strtoupper($this->long)])
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
        'masuco',
        'vitri',
        'loai',
        'n_phathien',
        'd_phathien',
        'n_xuly',
        'd_xuly',
        'n_hoancong',
        'nguyennhan',
        'cachxuly',
        'mataisan',
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
        'loaisuco_id',
        'nguyennhansuco_id',
        'tinhtrangsuco_id',
        'tinhtrang_id',        ];
    }
}
