<?php

	/**
	 * @ignore
	 */
	class takesurvey extends controller {
		/**
		 * @ignore
		 */
		public function get_new() {
			statics::requireAuthentication(1);
			$this->load('surveyModel');
			$surveys = $this->surveyModel->getAllByOwner(statics::$user['userid']);
			$this->setRef('surveys', $surveys);
			// render the page
			$this->view();
		}
		/**
		 * @ignore
		 */
		public function post_new() {
			statics::requireAuthentication(1);
			$this->load('publishSurveyModel');
				$input = array(
					'surveypublishid' => string::generateUuid(),
					'surveyid' => http::post('surveyid'),
					'revision' => '1',
					'ownerid' => statics::$user['userid'],
					'startdate' => http::post('startdate'),
					'enddate' => http::post('enddate'),
					'password' => http::post('password'),
					'type' => http::post('type'),
					'enabled' => http::post('enabled')
				);
				$insertSurvey = $this->publishSurveyModel->insert($input);
			if($insertSurvey > 0){
				echo "<script>alert('Survey Added Successfuly');</script>";
			}
			else {
				echo "<script>alert('Unexpected Error.');</script>";
			}
			$this->load('surveyModel');
			$surveys = $this->surveyModel->getAllByOwner(statics::$user['userid']);
			$this->setRef('surveys', $surveys);
			$this->view();
		}

		/**
		 * @ignore
		 */

		public function get_index($surveryid) {
			statics::requireAuthentication(1);
			$this->load('surveyModel');
			$this->load('questionModel');
			// gather all survey data from model
			$survey = $this->surveyModel->getDetail($surveryid);
			$questions = $this->questionModel->getBySurveyID($surveryid);
			$choices = array();
			foreach($questions as $question) {
				$choices[$question['questionid']] = $this->questionModel->getChoicesByQuestionID($question['questionid']);
			}


			// assign the user data to view
			$this->setRef('surveys', $survey);
			$this->setRef('choices', $choices);
			$this->setRef('questions', $questions);

			// render the page
			$this->view();
		}
		public function post_index($surveryid) {
			statics::requireAuthentication(1);
			$this->load('surveyModel');
			$this->load('questionModel');
			// gather all survey data from model
			$survey = $this->surveyModel->getDetail($surveryid);
			$questions = $this->questionModel->getBySurveyID($surveryid);
			$answers = array();
			foreach($questions as $question) {
				$answers[$question['questionid']] = http::post($question['questionid']);

				if($question['type'] == statics::QUESTION_MULTIPLE){
					$input = array(
						'surveyid' => $surveryid,
						'questionid' => $question['questionid'],
						'userid' => statics::$user['userid'],
						'questionchoiceid' => $answers[$question['questionid']],
						'value'=> null
					);
				} else {
					$input = array(
						'surveyid' => $surveryid,
						'questionid' => $question['questionid'],
						'userid' => statics::$user['userid'],
						'questionchoiceid' => null,
						'value'=> $answers[$question['questionid']]
					);
				}
				$this->questionModel->insertAnswer($input);
			}

			echo "<script>alert('Your Answers Recorded Succesfully.');</script>";
			mvc::redirect('home/index');
			// render the page
			$this->view();
		}

		/**
		 * @ignore
		 */
		public function get_edit($uSurveyPublishsId) {
			statics::requireAuthentication(1);
			$this->load('publishSurveyModel');
			// gather all survey data from model
			$tSurvey = $this->publishSurveyModel->get($uSurveyPublishsId);

			// assign the user data to view
			$this->set('publishSurveys', $tSurvey);
			// render the page
			$this->view();
		}

		/**
		 * @ignore
		 */
		public function post_edit($uSurveyPublishId) {
			statics::requireAuthentication(1);
			$this->load('publishSurveyModel');
			$update = array(
				'revision' => '1',
				'ownerid' => statics::$user['userid'],
				'startdate' => http::post('startdate'),
				'enddate' => http::post('enddate'),
				'password' => http::post('password'),
				'type' => http::post('type'),
				'enabled' => http::post('enabled')
			);
			// gather all survey data from model
			$tEditPublish = $this->publishSurveyModel->update($uSurveyPublishId, $update);
			if($tEditPublish > 0){
				echo "<script> alert('Record updated successfuly.');</script>";
			}
			else{
				echo "<script> alert('Unexpected Error. Try Again Later.');</script>";
			}
				$tSurvey = $this->publishSurveyModel->get($uSurveyPublishId);
			// assign the user data to view

			$this->set('publishSurveys', $tSurvey);
			// render the page
			$this->view();
		}

		/**
		 * @ignore
		 */
		public function get_report($uSurveyId) {
			statics::requireAuthentication(1);

			$this->load('surveyModel');

			// gather all survey data from model
			$tSurvey = $this->surveyModel->get($uSurveyId);
			
			// assign the user data to view
			$this->setRef('survey', $tSurvey);

			// render the page
			$this->view();
		}
	}

?>