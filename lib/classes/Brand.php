<?php
namespace Ml\Settings;

use CIBlockElement;
use CModule;

/**
 * Устанавливает бренд по умолчанию.
 * Class Brand
 * @package Ml\Settings
 */
class Brand extends Main
{
    protected array $request;
    private int $idBrand = IBLOCK_BRAND;
    protected string $propertyCode = "SET_DEFAULT";  // код свойства
    protected string $propertyValueOff = "N";
    protected int $propertyValueOn = 2604;

    public function __construct()
    {
        $this->request = parent::getRequest();

        if (!(CModule::IncludeModule("iblock"))){
            throw new Exception('Not module iblock.');
        }
    }

    public function init (){

        if (self::checkSetBefore()) {
            if (self::clearBrandDefault()) {
                $elem_id = self::setBrandDefault();
            }
        }

        return $elem_id;
    }

    /**
     * Находит у брендов свойства.
     * @return array|null
     */
    private function checkQuantityBrandDefault() : ?array{
        try {
            $arSelect = Array("ID", "NAME", "PROPERTY_SET_DEFAULT");
            $arFilter = Array("IBLOCK_ID"=>$this->idBrand, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "!PROPERTY_".$this->propertyCode => false);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
            while($obBrand = $res->GetNext())
            {
                $result[] = $obBrand['ID'];
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * Проверят наличие одинакового ид у элемента и запроса на установку.
     * @return int
     */
    private function checkSetBefore(): int{
        $BrandDefault = self::checkQuantityBrandDefault();
        $idChooseBrend = intval($this->request['choose_brand']);
        $result = true;

        if (count($BrandDefault) === 1) {
            if(current($BrandDefault) == $idChooseBrend){
                $result = false;
            }
        }else{
            $result = true;
        }
        return $result;
    }

    /**
     * Убирает установленные свойства у элементов.
     * @return int
     */
    private function clearBrandDefault(): int{
        $BrandDefault = self::checkQuantityBrandDefault();
        $result = true;

        if (count($BrandDefault)) {
            foreach ($BrandDefault as $elemId) {
                CIBlockElement::SetPropertyValuesEx($elemId, $this->idBrand, array($this->propertyCode => $this->propertyValueOff));
            }

            $result = count($BrandDefault);
        }

        return $result;
    }

    /**
     * Устанавливает свойства.
     * @return array
     */
    private function setBrandDefault () :array{
        try {
            $elemId = intval($this->request['choose_brand']);

            CIBlockElement::SetPropertyValuesEx($elemId, $this->idBrand, array($this->propertyCode => $this->propertyValueOn));
            $result['elem_id'] =  $elemId;

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

}