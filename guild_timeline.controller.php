<?php

/**
 * 길드 타임라인
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Guild_timelineController extends Guild_timeline
{
	/**
	 * 트리거 예제: 글 리스트를 불러올 때 실행
	 * 
	 * @param object $obj
	 */
	public function triggerbeforeListDocument($obj)
	{
		$config = $this->getConfig();
		if ($obj->module_srl != $config->guild_timeline_board_srl) return $this->createObject();
		
		$logged_info = Context::get('logged_info');

		$oGuildModel = getModel('guild');
		$oGuildBoardModel = getModel('guild_board');
		$guildList = $oGuildModel->GetGuildDataByMemberSrl($logged_info->member_srl);
		
		$guild_list = array();
		$guild_name = array();
		foreach($guildList as $inc => $data)
		{
			array_push($guild_list, $data->guild_data->guild_srl);
			$guild_name[$data->guild_data->guild_srl] = $data->guild_data->guild_name;
		}
		debugPrint($guild_name);
		
		$obj->guild_srl = $guild_list;
		$module_srl = $obj->module_srl;
		unset($obj->module_srl);
		
		$output = $oGuildBoardModel->getBoardListWithguild($obj);
		foreach($output->data as $inc => &$data)
		{
			$b_data = $oGuildBoardModel->getBoardData($data->document_srl);
			$data->variables['title'] = sprintf("[%s] %s", $guild_name[$b_data->guild_srl],$data->variables['title']);
		}
//		debugPrint($output);
		
		$obj->use_alternate_output = $output;
		return $this->createObject();
	}
	
	public function triggerbeforeInsertDocument($obj)
	{
		$config = $this->getConfig();
		if ($obj->module_srl == $config->guild_timeline_board_srl) return $this->createObject(-1, "해당 게시판에서 글을 쓸 수 없습니다.");
	}
}
