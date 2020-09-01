<?php

namespace App\gnr;

use App\ConstValue;
use App\hr\EmployeeGeneralInfo;
use App\hr\Position;
use Illuminate\Database\Eloquent\Model;

class GnrResponsibility extends Model
{
    protected $table = 'gnr_responsibilities';

    protected $fillable = [
        'position_id_fk',
        'emp_id_fk',
        'type_code',
        'id_list',
    ];

    const TYPE_CODE_LIST = [
        ConstValue::RESPONSIBILITY_TYPE_CODE_AREA => 'AREA',
        ConstValue::RESPONSIBILITY_TYPE_CODE_ZONE => 'ZONE',
        ConstValue::RESPONSIBILITY_TYPE_CODE_REGION => 'REGION',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeGeneralInfo::class, 'emp_id_fk');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id_fk');
    }

    public function getListTypeCode()
    {
        return self::TYPE_CODE_LIST;
    }

    public function getIdList()
    {
        return json_decode($this->id_list);
    }

    public function getBoundaries()
    {
        $boundaries = collect([]);
        if ($this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_REGION) {
            $boundaries = $this->getRegion();
        } elseif ($this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_AREA){
            $boundaries = $this->getArea();
        } elseif ($this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_ZONE){
            $boundaries = $this->getZone();
        }
        return $boundaries;
    }

    public function getRegion()
    {
        $regions = collect([]);
        if (count($this->getIdList()) > 0 && $this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_REGION) {
            $regions = GnrRegion::whereIn('id', $this->getIdList())->get();
        }
        return $regions;
    }

    public function getArea()
    {
        $areaList = collect([]);
        if (count($this->getIdList()) > 0 && $this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_AREA) {
            $areaList = GnrArea::whereIn('id', $this->getIdList())->get();
        }
        return $areaList;
    }


    public function getZone()
    {
        $zoneList = collect([]);
        if (count($this->getIdList()) > 0 && $this->type_code == ConstValue::RESPONSIBILITY_TYPE_CODE_ZONE) {
            $zoneList = GnrZone::whereIn('id', $this->getIdList())->get();
        }
        return $zoneList;
    }


}