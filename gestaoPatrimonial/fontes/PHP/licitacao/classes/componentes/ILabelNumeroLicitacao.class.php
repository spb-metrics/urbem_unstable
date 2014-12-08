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

    * Arquivo do componente ILabelNumeroLicitacao
    * Data de Criação: 26/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    $Revision: 21642 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-04-05 16:22:31 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-03.05.16
*/

include_once ( CLA_OBJETO );

class  ILabelNumeroLicitacao extends Objeto
{
    public $stExercicio;
    public $inNumEdital;
    public $inNumLicitacao;
    public $obLblExercico;
    public $obLblEntidade;
    public $obLblModalidade;
    public $obLblLicitacao;
    public $obLblProcesso;
    public $obLblObjeto;
    public $boMostrarObjeto;
    public $boMostrarDataHoraLic;
    public $boFiltro = "true";
    
    public function setExercicio($valor) { $this->stExercicio = $valor; }
    public function setNumEdital($valor) { $this->inNumEdital = $valor; }
    public function setNumLicitacao($valor) { $this->inNumLicitacao = $valor; }
    public function setCodPreEmpenho($valor) { $this->inCodPreEmpenho = $valor; }
    public function setCodEntidade($valor) { $this->inCodEntidade = $valor; }
    public function setMostrarObjeto($valor) { $this->boMostrarObjeto = $valor; }
    public function setMostrarDataHoraLic($valor) { $this->boMostrarDataHoraLic = $valor; }
    public function setBoFiltro($valor) { $this->boFiltro = $valor; }

    public function getExercicio() { return $this->stExercicio; }
    public function getNumEdital() { return $this->inNumEdital; }
    public function getNumLicitacao() { return $this->inNumLicitacao; }
    public function getCodPreEmpenho() { return $this->inCodPreEmpenho; }
    public function getCodEntidade() { return $this->inCodEntidade; }
    public function getMostrarObjeto() { return $this->boMostrarObjeto; }
    public function getMostrarDataHoraLic() { return $this->boMostrarDataHoraLic; }    
    public function getBoFiltro() { return $this->boFiltro; }

    public function ILabelNumeroLicitacao(&$obForm)
    {
        parent::Objeto();

        $this->obLblLicitacao = new Label();
        $this->obLblLicitacao->setName ( 'stNumeroLicitacao' );
        $this->obLblLicitacao->setRotulo( 'Número da Licitação' );

        $this->obLblExercicio = new Label();
        $this->obLblExercicio->setName  ( 'stExercicioLicitacao'   );
        $this->obLblExercicio->setRotulo( 'Exercício da Licitação' );

        $this->obLblEntidade = new Label();
        $this->obLblEntidade->setName  ( 'stEntidadeLicitacao'  );
        $this->obLblEntidade->setRotulo( 'Entidade' );

        $this->obLblModalidade = new Label();
        $this->obLblModalidade->setName( 'stModalidadeLicitacao' );
        $this->obLblModalidade->setRotulo( 'Modalidade' );

        $this->obLblProcesso  = new Label();
        $this->obLblProcesso->setName  ( 'stProcessoAdministrativo' );
        $this->obLblProcesso->setRotulo( 'Processo Administrativo'  );
    }

    public function geraFormulario(&$obFormulario)
    {
        //if para caso não haja o num_edital setado, pegar o edital a partir do cod_licitacao
        if (!$this->getNumEdital()) {
            include_once(TLIC."TLicitacaoLicitacao.class.php");
            $obTLicitacao = new TLicitacaoLicitacao();
            $obTLicitacao->setDado('cod_licitacao',$this->getNumLicitacao());
            $obTLicitacao->setDado('exercicio',$this->getExercicio());
            $obTLicitacao->recuperaLicitacaoEdital($rsLicitacaoEdital);
            $this->setNumEdital($rsLicitacaoEdital->getCampo('num_edital'));
        }

        include_once ( TLIC."TLicitacaoEdital.class.php" );

        if ($this->boMostrarObjeto) {

            $this->obLblObjeto  = new Label();
            $this->obLblObjeto->setName  ( 'stObjeto' );
            $this->obLblObjeto->setRotulo( 'Objeto'  );
            if($this->boMostrarDataHoraLic){
                $this->obLblDataEntrega = new Label();
                $this->obLblDataEntrega->setName  ( 'stDataEntrega' );
                $this->obLblDataEntrega->setRotulo( 'Data da Entrega das Propostas'  );

                $this->obLblHoraEntrega = new Label();
                $this->obLblHoraEntrega->setName  ( 'stHoraEntrega' );
                $this->obLblHoraEntrega->setRotulo( 'Hora'  );
            }

        }

        $obTLicitacaoEdital = new TLicitacaoEdital();

        $obTLicitacaoEdital->setDado( 'exercicio', $this->getExercicio() );
        $obTLicitacaoEdital->setDado( 'num_edital', $this->getNumEdital() );
        
        if($this->boFiltro == "true"){
            $stFiltro = " and lh.num_homologacao is null ";
        }else{
            $stFiltro = "";
        }
        
        if ($this->boMostrarObjeto) {
            $obTLicitacaoEdital->recuperaEditalObjeto( $rsRecordSet, $stFiltro );
        } else {
           $obTLicitacaoEdital->recuperaEdital( $rsRecordSet, $stFiltro );
        }
        
        $this->obLblLicitacao->setValue ( $rsRecordSet->getCampo( 'cod_licitacao' ) );
        $this->obLblExercicio->setValue ( $rsRecordSet->getCampo( 'exercicio_licitacao' ) );
        $this->obLblEntidade->setValue ( $rsRecordSet->getCampo( 'cod_entidade' ).'-'.$rsRecordSet->getCampo( 'nom_entidade' ) );
        $this->obLblModalidade->setValue ( $rsRecordSet->getCampo( 'cod_modalidade' ).'-'.$rsRecordSet->getCampo( 'nom_modalidade' ) );
        $this->obLblProcesso->setValue ( str_pad($rsRecordSet->getCampo( 'cod_processo' ), 5, '0', STR_PAD_LEFT).'/'.$rsRecordSet->getCampo( 'exercicio_processo' ) );

        if ($this->boMostrarObjeto) {
            $this->obLblObjeto->setValue ( $rsRecordSet->getCampo( 'cod_objeto' )." - ".stripslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSet->getCampo( 'descricao' ))))) );
            if($this->boMostrarDataHoraLic){
                $this->obLblDataEntrega->setValue ( $rsRecordSet->getCampo( 'dt_entrega_propostas' ) );
                $this->obLblHoraEntrega->setValue ( $rsRecordSet->getCampo( 'hora_entrega_propostas' ) );
            }
        }

        $obFormulario->addComponente( $this->obLblLicitacao  );
        $obFormulario->addComponente( $this->obLblExercicio  );
        $obFormulario->addComponente( $this->obLblEntidade   );
        $obFormulario->addComponente( $this->obLblModalidade );
        $obFormulario->addComponente( $this->obLblProcesso   );

         if ($this->boMostrarObjeto) {                                                                                                 
             $obFormulario->addComponente( $this->obLblObjeto     );      
             if($this->boMostrarDataHoraLic){
                $obFormulario->addComponente( $this->obLblDataEntrega);                                                                    
                $obFormulario->addComponente( $this->obLblHoraEntrega);     
             }             
        }
    }

    public function geraFormularioEmpenho(&$obFormulario)
    {
        include_once ( TLIC."TLicitacaoLicitacaoPreEmpenho.class.php" );
        $obTLicitacaoLicitacaoPreEmpenho = new TLicitacaoLicitacaoPreEmpenho();

        $obTLicitacaoLicitacaoPreEmpenho->setDado( 'cod_pre_empenho', $this->getCodPreEmpenho() );
        $obTLicitacaoLicitacaoPreEmpenho->setDado( 'exercicio', $this->getExercicio() );
        $obTLicitacaoLicitacaoPreEmpenho->setDado( 'cod_entidade', $this->getCodEntidade() );

        if ( $this->getCodPreEmpenho() && $this->getExercicio() && $this->getCodEntidade() ) {
            $obTLicitacaoLicitacaoPreEmpenho->recuperaListaLicitacaoPreEmpenho( $rsRecordSet, $stFiltro );
            $this->obLblLicitacao->setValue ( $rsRecordSet->getCampo( 'exercicio_licitacao' ).$rsRecordSet->getCampo( 'cod_entidade' ).$rsRecordSet->getCampo( 'cod_modalidade' ).$rsRecordSet->getCampo( 'cod_licitacao' ) );
            $this->obLblExercicio->setValue ( $rsRecordSet->getCampo( 'exercicio_licitacao' ) );
            $this->obLblEntidade->setValue ( $rsRecordSet->getCampo( 'cod_entidade' ).'-'.$rsRecordSet->getCampo( 'nom_entidade' ) );
            $this->obLblModalidade->setValue ( $rsRecordSet->getCampo( 'cod_modalidade' ).'-'.$rsRecordSet->getCampo( 'nom_modalidade' ) );

            $obFormulario->addComponente( $this->obLblLicitacao  );
            $obFormulario->addComponente( $this->obLblExercicio  );
            $obFormulario->addComponente( $this->obLblEntidade   );
            $obFormulario->addComponente( $this->obLblModalidade );
        }
    }

}
?>
