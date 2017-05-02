<?php
namespace jasonwynn10\murder;

class MurderSession {
	private $traitor;
	private $detective;
	private $innocent = [];
	public function __construct(Player $traitor, Player $detective, Player ...$innocent) {
		//TODO
	}
}
