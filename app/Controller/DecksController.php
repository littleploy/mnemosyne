<?php
/**
 * Created by PhpStorm.
 * User: chongos
 * Date: 4/23/15 AD
 * Time: 12:23 PM
 */

class DecksController extends AppController {

    public $uses = ['Deck', 'Category', 'Card', 'Tag', 'User', 'DeckTag', 'FavoriteDeck', 'Score'];

    public function index() {
        $this->__getCategories();
    }
    
    public function category($category_id = null) {
        $this->__getCategories();
        $this->set('category_id', $category_id);
        $decks = $this->Deck->find('all', [
            'conditions' => ['Deck.status' => 1,'Category.id' => $category_id],
            'recursive'=> 1
        ]);
        
        $favoriteDeck = $this->FavoriteDeck->find('list', [
            'conditions' => ['FavoriteDeck.user_id' => $this->Auth->User('id')],
            'fields' => ['FavoriteDeck.deck_id'],
            'recursive'=> -1
        ]);
        $favorites = [];
        foreach($favoriteDeck as $key => $value) {
            $favorites[] = $value;
        }
        $this->set('favorite', $favorites);
        
        $tags = $this->Tag->find('list', [
            'recursive'=> -1,
            'fields' => ['Tag.id','Tag.name']
        ]);
        
        $data = [];
        foreach($decks as $deck) {
            $deckTag = [];
            foreach($deck['DeckTag'] as $tag) {
                $id = $tag['tag_id'];
                $deckTag[] = [
                    'id' => $id, 
                    'name' => $tags[$id]  
                ];
            }
            $data[] = [
                        'Deck' => $deck['Deck'],
                        'Card' => count($deck['Card']),
                        'User' => [
                            'id' => $deck['User']['id'],
                            'username' => $deck['User']['username']
                            ],
                        'Category' => $deck['Category'],
                        'Tag' => $deckTag
                      ];   
        }
        $this->set('data', $data);
    }
    
    public function add() {
        $this->__getCategories();
        
        if ($this->request->is('post')) { 
            $tags = [];
            foreach(explode(",,;",$this->request->data['Deck']['tags']) as $tag)
                $tags[] = ['Tag' => ['name' => $tag]];

            $cards = [];
            $count = 1;
            foreach(json_decode($this->request->data['Deck']['cards'], true) as $card) {
                $front = '{\''.$card['front']['type'].'\':\''.$card['front']['value'].'\'}';
                $back = '{\''.$card['back']['type'].'\':\''.$card['back']['value'].'\'}';
                $cards[] = ['front' => $front, 'back' => $back, 'sort_number' => $count];
                $count++;
            }
            
            $data = [
                'Category' => ['id' => $this->request->data['Deck']['category_id']],
                'Deck' => [
                    [
                        'name' => $this->request->data['Deck']['name'],
                        'description' => $this->request->data['Deck']['description'],
                        'user_id' => $this->Auth->user('id'),
                        'Card' => $cards,
                        'DeckTag' => $tags
                    ]
                ]
            ];

            if($this->Category->saveAssociated($data, array('deep' => true))) {
                $this->Session->setFlash("Success");
                $this->redirect(['action' => 'edit', $this->Deck->getLastInsertId()]);
            } else {
                $this->Session->setFlash("Error");
            }

        }
    }
    
    public function edit($deck_id = null) {
        $deck = $this->Deck->find('first', [
            'conditions' => ['Deck.id' => $deck_id],
            'recursive'=> -1
        ]);
        
        // Published will redirect
        if($deck['Deck']['status'])
            $this->redirect(['action' => 'stat', $deck_id]);
        
        $cards = $this->Card->find('all', [
            'conditions' => ['Card.deck_id' => $deck_id],
            'order' => ['Card.sort_number'],
            'fields' => ['Card.id', 'Card.front', 'Card.back', 'Card.sort_number']
        ]);
        $this->set('cards', json_encode($cards));
        $this->__getCategories();
        $this->set('deck', $deck);
        $this->set('tags', $this->__getTags($deck_id));
        
        if ($this->request->is('post')) { 
            $tags = [];
            foreach(explode(",,;",$this->request->data['Deck']['tags']) as $tag)
                $tags[] = ['Tag' => ['name' => $tag]];
            $data = [
                'Category' => ['id' => $this->request->data['Deck']['category_id']],
                'Deck' => [
                    [  
                        'id' => $deck_id,
                        'name' => $this->request->data['Deck']['name'],
                        'description' => $this->request->data['Deck']['description'],
                        'status' => $this->request->data['Deck']['status'],
                        'Card' => json_decode($this->request->data['Deck']['cardOrder'], true),
//                        'DeckTag' => $tags
                    ]
                ]
            ];
            
            if($this->Category->saveAssociated($data, array('deep' => true))) {
                $delCard = json_decode($this->request->data['Deck']['cardDelete'], true);
                if(!empty($delCard)) {
                    if($this->Card->deleteAll($delCard)) {
                        $this->Session->setFlash("Success");
                        $this->redirect(['action' => 'edit', $deck_id]);
                    } else {
                        $this->Session->setFlash("Error");
                        return;
                    }
                }
                $this->Session->setFlash("Success");
                $this->redirect(['action' => 'edit', $deck_id]);
            } else {
                $this->Session->setFlash("Error");
            }
        }
    }
    
    public function stat($deck_id=null) {
        $favoriteDeck = $this->FavoriteDeck->find('all', [
            'conditions' => ['deck_id' => $deck_id],
            'recursive' => 0,
            'fields' => [
                'User.id', 'User.username'
            ]
        ]);
        $this->set('favoriteDeck',$favoriteDeck);
        
        $deck = $this->Deck->find('first', [
            'conditions' => ['id' => $deck_id],
            'recursive' => -1,
            'fields' => [
                'Deck.name', 'Deck.description'
            ]
        ]);
        
        $this->set('deck', $deck);
        
        $scores = $this->Score->find('all', [
            'conditions' => ['deck_id' => $deck_id],
            'recursive' => 0,
            'fields' => [
                'Score.score', 'Score.modified', 'User.id', 'User.username'
            ]
        ]);
        
        $this->set('scores', $scores);
    }
    
    
   public function reviewMode($deckID = null) {
		
		// Enter 'Reviewing' mode

        if (! is_null($deckID)) {
	        
	        // Store deckID for later usage
	        $this->Session->write('deckID', $deckID);

            if ($this->request->is('get')) {

                $deck = $this->Deck->find('first', array('conditions' => array('Deck.id' => $deckID),
                    'fields' => array('name'),
                    'recursive' => -1
                ));

                $cards = $this->Card->find('all', array('conditions' => array('deck_id' => $deckID),
                    'fields' => array('front', 'back'),
                    'order' => 'sort_number ASC',
                    'recusive' => -1
                ));
                
				$answer = array();
                
                foreach ($cards as $card) {
	            	array_push($answer, $card['Card']['back']);    
                }
                                
                // Store the answers for later validation
                $this->Session->write('answer', $answer);
                
                $this->set('cards', $cards);

                $this->set('deck_name', $deck['Deck']['name']);

            }

        }
        else {

            // $this->Session-setFlash();
            // $this->redirect();

        }

	}

	public function learnMode($deckID = null) {

        // Enter 'Learning mode'
        if (! is_null($deckID)) {
	        
			// Store deckID for later usage
	        $this->Session->write('deckID', $deckID);

            if ($this->request->is('get')) {

                // If deckID != null then go to the learn page

                $deck = $this->Deck->find('first', array('conditions' => array('Deck.id' => $deckID),
                    'fields' => array('name'),
                    'recursive' => -1
                ));

                $cards = $this->Card->find('all', array('conditions' => array('deck_id' => $deckID),
                    'fields' => array('front', 'back'),
                    'order' => 'sort_number ASC',
                    'recusive' => -1
                ));

                $this->set('deck_name', $deck['Deck']['name']);
                $this->set('cards', $cards);

            }

        }
        else {

            // $this->Session->setFlash();
            // $this->redirect();

        }
		
	}
	
	public function validateCard() {
		
		function _prepareString($string) {
			$string = str_replace('{', '', $string);
			$string = str_replace('}', '', $string);
			$string = str_replace('\'', '', $string);
			$string = explode(':', $string, 2);
			$result = array('type' => $string[0], 'data' => $string[1]);
			return $result;
		}
		
		$this->autoRender = false;
		
		$this->request->allowMethod(array('ajax'));
		
		if ($this->request->is('ajax')) {
			
			$answer = $this->request->data['value'];
				
			// initial the score
			if (! $this->Session->check('score')) {
				$this->Session->write('score', 0);
			}
			
			$id = $this->request->data['id'];
				
			$currentScore = $this->Session->read('score');
			
			$correctAnswer = $this->Session->read('answer');
							
			if (_prepareString($correctAnswer[$id])['data'] == $answer) {
				$this->Session->write('score', $currentScore + 1);
				$return['action'] = 'correct';
			}
			else {
				$return['action'] = 'wrong';
			}
			
			// force redirection if it is a last card
			$isLastCard = $this->request->data['lastCard'];
			if ($isLastCard == 1) {
				$return['action'] = 'redirect';
				$return['value'] = Router::url(array('controller' => 'Decks', 'action' => 'result'));
			}
			
			$return['json'] = json_encode($return);
			echo json_encode($return);
			
		}
			
		die();
						
	}
	
	public function result() {
		
		$score = $this->Session->read('score');
		$userID = $this->Auth->user('id');
		$deckID = $this->Session->read('deckID');
		
		// Save latest user'sscore
		/*
		$data = array(
			'User' => array('id' => $userID),
			'Score' => array(
				'score' => $score,
 				//'user_id' => ,
				'deck_id' => $deckID
			)
		);
		
		$this->User->saveAssociated($data);
		*/
		
		
		// Get the latest 10 results
		
		$deck = $this->Deck->find('first', array('conditions' => array('Deck.id' => $deckID),
                    'fields' => array('name'),
                    'recursive' => 1
                ));
                
        $this->set('user_name', $this->Auth->user('username'));
                
        $this->set('total_score', sizeof($deck['Card']));
        		                
        $this->set('deck_name', $deck['Deck']['name']);
		
		$this->set('score', $score);
		
	}
    function __getTags($deck_id) {
        $tags = $this->DeckTag->find('all', [
            'conditions' => ['DeckTag.deck_id' => $deck_id],
            'fields' => ['Tag.name']
        ]);
        $tagStr = '';
        foreach($tags as $tag)
            $tagStr .= $tag['Tag']['name'].',,;';
        return substr($tagStr, 0, -3);
    }

    function __getCategories() {
        $list = $this->Category->find('list', [
            'recursive'=> -1,
            'order' => [
                'Category.name' => 'ASC'
            ],
            'fields' => [
                'Category.id','Category.name'
            ]
        ]);
        $this->set('categories', $list);
    }
  
}
