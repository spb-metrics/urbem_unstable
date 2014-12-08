<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 27/02/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Id: IMontaSolicitacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.04.05, uc-03.03.05, uc-03.03.06

*/

include_once ( CLA_OBJETO );

class  IMontaSolicitacao extends Objeto
{
    public $obForm;
    public $obExercicio;
    public $obITextBoxSelectEntidade;
    public $obPopUpSolicitacao;
    public $inCodSolicitacao;
    public $stTipoBusca;
    public $stName;
    public $stRotulo;
    public $boObrigatorioBarra;
    public $stDefinicao;
    public $boNull;
    public $boNullBarra;
    public $inCodSolicitacaoExcluida;
    public $boMostraReduzido;
    public $inTotalEntidade;

    public function setCodSolicitacao($inCodSolicitacao)
    {
       $this->inCodSolicitacao = $inCodSolicitacao;
    }
    public function setTipoBusca($stTipoBusca)
    {
       $this->stTipoBusca = $stTipoBusca;
    }
    public function getTipoBusca() { return $this->stTipoBusca; }

    public function setObrigatorioBarra($valor = true) { $this->boObrigatorioBarra = $valor; }
    public function getObrigatorioBarra() { return $this->boObrigatorioBarra; }

    public function setNullBarra($valor = true) { $this->boNullBarra = $valor; }
    public function getNullBarra() { return $this->boNullBarra; }

    public function getDefinicao() { return $this->stDefinicao; }
    public function setDefinicao($valor = "IMONTASOLICITACAO") { $this->stDefinicao = $valor; }

    public function getNull() { return $this->boNull; }
    public function setNull($valor = true) { $this->boNull = $valor;  }

    public function setName($valor) { $this->stName = $valor;}
    public function getName() { return $this->stName; }

    public function setRotulo($valor) {  $this->stRotulo = $valor;}
    public function getRotulo() { return $this->stRotulo; }

    public function setCodSolicitacaoExcluida($valor = true) { $this->inCodSolicitacaoExcluida = $valor; }
    public function getCodSolicitacaoExcluida() { return $this->inCodSolicitacaoExcluida; }

    public function setTotalEntidade($valor = true) { $this->inTotalEntidade = $valor; }
    public function getTotalEntidade() { return $this->inTotalEntidade; }

    public function IMontaSolicitacao(&$obForm)
    {
        parent::Objeto();

        $pgOcul  = CAM_GP_COM_PROCESSAMENTO.'OCIMontaSolicitacao.php?'.Sessao::getId();
        $this->obForm = &$obForm;

        $this->obExercicio = new Exercicio;
        $this->obExercicio->setId ('stExercicioSolicitacao');
        $this->obExercicio->setName('stExercicioSolicitacao');
        $this->obExercicio->setObrigatorioBarra( $this->getObrigatorioBarra() );
        $this->obExercicio->setNullBarra( $this->getNullBarra() );

        include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
        $this->obITextBoxSelectEntidade = new ITextBoxSelectEntidadeUsuario;

        $this->obITextBoxSelectEntidade->obTextBox->setId   ('inCodEntidadeSolicitacao');
        $this->obITextBoxSelectEntidade->obTextBox->setName ('inCodEntidadeSolicitacao');
        $this->obITextBoxSelectEntidade->obTextBox->setObrigatorioBarra( $this->getObrigatorioBarra() );
        $this->obITextBoxSelectEntidade->obTextBox->setNullBarra($this->getNullBarra());

        $this->obITextBoxSelectEntidade->obSelect->setName  ('stNomEntidadeSolicitacao');
        $this->obITextBoxSelectEntidade->obSelect->setId    ('stNomEntidadeSolicitacao');
        $this->obITextBoxSelectEntidade->obSelect->setObrigatorioBarra( $this->getObrigatorioBarra() );
        $this->obITextBoxSelectEntidade->obSelect->setNullBarra( $this->getNullBarra() );
        $this->obITextBoxSelectEntidade->obSelect->setNull($this->boNull);

        $this->obPopUpSolicitacao = new PopUp     ($obForm);
        $this->obPopUpSolicitacao->setRotulo                 ( 'Solicitação'              );
        $this->obPopUpSolicitacao->setTitle                  ( 'Informe a solicitação.'   );
        $this->obPopUpSolicitacao->setId                     ( 'stNomSolicitacao'     );

        $this->obPopUpSolicitacao->obCampoCod->setName       ( "inCodSolicitacao"     );
        $this->obPopUpSolicitacao->obCampoCod->setRotulo     ( 'Solicitação'          );
        $this->obPopUpSolicitacao->obCampoCod->setId         ( 'inCodSolicitacao'     );
        $this->obPopUpSolicitacao->obImagem->setId           ( 'imgSolicitacao'       );
        $this->obPopUpSolicitacao->obCampoCod->setSize       ( 10                     );
        $this->obPopUpSolicitacao->obCampoCod->setMaxLength  ( 9                      );
        $this->obPopUpSolicitacao->obCampoCod->setAlign      ( "left"                 );
        $this->obPopUpSolicitacao->obCampoCod->setNull       ($this->boNull);
        $this->obPopUpSolicitacao->obCampoCod->setObrigatorioBarra( $this->getObrigatorioBarra() );
        $this->obPopUpSolicitacao->obCampoCod->setNullBarra( $this->getNullBarra() );

        $this->obExercicio->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inExercicio='+this.value,'preencheEntidade');\n" );

        $this->obITextBoxSelectEntidade->obREntidade->listarUsuariosEntidade( $rsEntidades ) ;

        if ( $rsEntidades->getNumLinhas() > 0 ) {
            $this->obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."','limpaSolicitacao');\n" );
            $this->obITextBoxSelectEntidade->obSelect->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."','limpaSolicitacao');\n ");
            $this->setTotalEntidade($rsEntidades->getNumLinhas());
        } else
            $this->setTotalEntidade(0);

        $this->stTipoBusca = "solicitacao";
    }

    public function geraFormulario(&$obFormulario)
    {

        $pgOcul  = CAM_GP_COM_PROCESSAMENTO.'OCIMontaSolicitacao.php?'.Sessao::getId();
        if ($this->inCodSolicitacao != "") {
          include_once(CAM_GP_COM_MAPEAMENTO . "TComprasSolicitacao.class.php");
          $obTMapeamento          = new TComprasSolicitacao();
          $rsRecordSet            = new Recordset;
          $obTMapeamento->setDado('cod_solicitacao', $this->obITextBoxSelectEntidade->obSelect->getValue());
          $obTMapeamento->setDado('cod_entidade', $this->obITextBoxSelectEntidade->obTextBox->getValue());
          $obTMapeamento->setDado('exericicio', $this->obExercicio->getValue());
          $obTMapeamento->recuperaPorChave($rsRecordSet);
          $this->obPopUpSolicitacao->obCampoCod->setValue($this->obITextBoxSelectEntidade->obSelect->getValue());
          $this->obPopUpSolicitacao->setValue($rsRecordSet->getCampo('observacao'));
        }

        $this->obPopUpSolicitacao->setFuncaoBusca("abrePopUp('".CAM_GP_COM_POPUPS."solicitacao/LSManterSolicitacao.php','".$this->obForm->getName()."', '".$this->obPopUpSolicitacao->obCampoCod->getName()."','". $this->obPopUpSolicitacao->getId()."','&stTipoBusca=".$this->getTipoBusca()."&inCodEntidade='+document.getElementById('".$this->obITextBoxSelectEntidade->obTextBox->getId()."').value+'&stExercicio='+document.getElementById('".$this->obExercicio->getId()."').value+'&inCodSolicitacaoExcluida=".$this->inCodSolicitacaoExcluida."','".Sessao::getId()."','800','550');");
        $this->obPopUpSolicitacao->obCampoCod->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."&inCodSolicitacao='+this.value+'&stExercicio='+document.getElementById('".$this->obExercicio->getId()."').value+'&inCodEntidade='+document.getElementById('".$this->obITextBoxSelectEntidade->obTextBox->getId()."').value+'&campoDesc=".$this->obPopUpSolicitacao->getId()."&campoCod=".$this->obPopUpSolicitacao->obCampoCod->getId()."&inCodSolicitacaoExcluida=".$this->inCodSolicitacaoExcluida."','".$this->getTipoBusca()."'); ");

        $obFormulario->addComponente( $this->obExercicio );
        $obFormulario->addComponente( $this->obITextBoxSelectEntidade );
        $obFormulario->addComponente( $this->obPopUpSolicitacao);

        // Seta o total de entidades disponíveis.
        $obTotalEntidade = new Hidden;
        $obTotalEntidade->setName("HdnTotalEntidade");
        $obTotalEntidade->setId("HdnTotalEntidade");
        $obTotalEntidade->setValue($this->getTotalEntidade());
        $obFormulario->addHidden($obTotalEntidade);

    }
}
?>
