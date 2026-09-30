<?php
declare(strict_types=1); require_once __DIR__.'/../includes/auth.php'; header('Content-Type: application/json; charset=utf-8');
$ids=json_decode(file_get_contents('php://input'),true)['ids']??[]; $ids=array_values(array_filter(array_map('intval',$ids))); if(!$ids){echo json_encode(['status'=>'empty','issues'=>[],'message'=>'Выберите комплектующие']);exit;}
$in=implode(',',array_fill(0,count($ids),'?')); $s=db()->prepare("SELECT * FROM components WHERE id IN ($in)"); $s->execute($ids); $items=$s->fetchAll(); $by=[]; foreach($items as $i)$by[$i['type']]=$i; $issues=[]; $warnings=[];
if(isset($by['cpu'],$by['motherboard']) && $by['cpu']['socket']!==$by['motherboard']['socket']) $issues[]='CPU и материнская плата используют разные сокеты.';
if(isset($by['motherboard'],$by['ram']) && $by['motherboard']['memory_type']!==$by['ram']['memory_type']) $issues[]='Тип памяти RAM не поддерживается материнской платой.';
$tdp=0; foreach(['cpu','gpu'] as $t) $tdp+=(int)($by[$t]['tdp']??0); if(isset($by['psu']) && (int)$by['psu']['wattage'] < $tdp+100) $issues[]='Мощности БП недостаточно: нужен запас минимум 100 Вт.'; elseif(isset($by['psu']) && (int)$by['psu']['wattage'] < $tdp+200) $warnings[]='Запас мощности БП небольшой — лучше рассмотреть модель мощнее.';
$status=$issues?'error':($warnings?'warning':'success'); echo json_encode(['status'=>$status,'issues'=>$issues,'warnings'=>$warnings,'tdp'=>$tdp,'message'=>$issues?'Есть несовместимости':($warnings?'Нужна проверка':'Все выбранные компоненты совместимы')],JSON_UNESCAPED_UNICODE);
