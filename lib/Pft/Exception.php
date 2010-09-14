<?php
/**
 * Pft 的 异常
 * 所有的错误在这里有 代号
 *
 */
class Pft_Exception extends Exception{
	const EXCEPTION_CODE_UNKNOWN	= 0;	//未知异常
	const EXCEPTION_NEED_LOGIN		= 1;	//需要登录
	const EXCEPTION_NO_PRIVILEGE	= 2;	//没有权限
}

