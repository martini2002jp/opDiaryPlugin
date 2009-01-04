<?php

/**
 * base actions class for the opDiaryPlugin.
 *
 * @package    OpenPNE
 * @subpackage diary
 * @author     Rimpei Ogawa <ogawa@tejimaya.com>
 */
class opDiaryPluginActions extends sfActions
{
  public function preExecute()
  {
    if (is_callable(array($this->getRoute(), 'getObject')))
    {
      $object = $this->getRoute()->getObject();
      if ($object instanceof Diary)
      {
        $this->diary = $object;
        $this->member = $this->diary->getMember();
      }
      elseif ($object instanceof DiaryComment)
      {
        $this->diaryComment = $object;
        $this->diary = $this->diaryComment->getDiary();
        $this->member = $this->diary->getMember();
      }
      elseif ($object instanceof Member)
      {
        $this->member = $object;
      }
    }
  }

  public function postExecute()
  {
    if (empty($this->member))
    {
      $this->member = $this->getUser()->getMember();
    }

    $this->setNavigation($this->member);
  }

  protected function setNavigation(Member $member)
  {
    if ($member->getId() !== $this->getUser()->getMemberId())
    {
      sfConfig::set('sf_navi_type', 'friend');
      sfConfig::set('sf_navi_id', $member->getId());
    }
  }

  protected function isAuthor()
  {
    if ($this->diary->getMemberId() === $this->getUser()->getMemberId())
    {
      return true;
    }

    return false;
  }

  protected function isViewable()
  {
    return DiaryPeer::isViewable($this->diary, $this->getUser()->getMemberId());
  }
}