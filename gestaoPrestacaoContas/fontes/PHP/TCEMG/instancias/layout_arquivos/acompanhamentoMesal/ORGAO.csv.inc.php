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
  * Página de Include Oculta - Exportação Arquivos TCEMG - ORGAO.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: ORGAO.csv.inc.php 61575 2015-02-10 12:53:21Z franver $
  * $Date: 2015-02-10 10:53:21 -0200 (Ter, 10 Fev 2015) $
  * $Author: franver $
  * $Rev: 61575 $
  *
*/

/**
* ORGAO.csv | Autor : Michel Teixeira
*/

include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoOrgao.class.php";

$rsRecordSetORGAO10 = new RecordSet();
$rsRecordSetORGAO11 = new RecordSet();
$stVersao = 'Versão: '.Sessao::getVersao();

$obTTCEMGConfiguracaoOrgao               = new TTCEMGConfiguracaoOrgao;
$obTTCEMGConfiguracaoOrgao->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGConfiguracaoOrgao->setDado('entidade',$stEntidades);

if(Sessao::getExercicio() >= 2015){
    $obTTCEMGConfiguracaoOrgao->setDado('versao',$stVersao);
    $obTTCEMGConfiguracaoOrgao->recuperaOrgao2015($rsRecordSetORGAO10);
}else {
    $obTTCEMGConfiguracaoOrgao->recuperaOrgao($rsRecordSetORGAO10);
}

$obTTCEMGConfiguracaoOrgao->setDado('dt_inicial', $stDataInicial);
$obTTCEMGConfiguracaoOrgao->setDado('dt_final'  , $stDataFinal);
$obTTCEMGConfiguracaoOrgao->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGConfiguracaoOrgao->setDado('entidade'  , $stEntidades);
$obTTCEMGConfiguracaoOrgao->recuperaOrgaoResponsavel($rsRecordSetORGAO11);

//Tipo Registro 99
$arRecordSetORGAO99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaORGAO99 = new RecordSet();
$rsRecuperaORGAO99->preenche($arRecordSetORGAO99);

if (count($rsRecordSetORGAO10->getElementos()) > 0) {
    $inCount=0;
    foreach ($rsRecordSetORGAO10->getElementos() as $arORGAO10) {
        $inCount++;
        $stChave = $arORGAO10['chave'];
         
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arORGAO10));
         
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
 
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
 
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
 
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpjorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        
        if( Sessao::getExercicio() >= 2015 ) {
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("versao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
        }
 
        if (count($rsRecordSetORGAO11->getElementos()) > 0) {
            foreach ($rsRecordSetORGAO11->getElementos() as $arORGAO11) {
                $stChave1 = $arORGAO11['chave'];
               
                if ($stChave === $stChave1) {
                    $rsBloco = 'rsBloco_'.$inCount;
                    unset($$rsBloco);
                    $$rsBloco = new RecordSet();
                    $$rsBloco->preenche(array($arORGAO11));
                  
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);
         
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporesponsavel");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cartident");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgemissorci");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(10);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
                  
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("crccontador");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(11);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ufcrccontador");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
                  
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cargoorddespdeleg");            
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtinicio");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtfinal");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
      
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
                }
            }
        }
    }
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaORGAO99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetORGAO10 = null;
$rsRecordSetORGAO11 = null;
$obTTCEMGConfiguracaoOrgao = null;
$rsRecuperaORGAO99  = null;

?>