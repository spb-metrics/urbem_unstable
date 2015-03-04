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
    * Página de Include Oculta - HOMOLOGAÇÃO DA LICITAÇÃO

    * Data de Criação   : 28/02/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore
    * $Id: HML.inc.php 61704 2015-02-26 15:50:25Z michel $
    * $Rev: 61704 $
    * $Author: michel $
    * $Date: 2015-02-26 12:50:25 -0300 (Qui, 26 Fev 2015) $

*/

include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOHML.class.php";

$rsRecordSeTTCMGOHML10 = new RecordSet();
$rsRecordSeTTCMGOHML20 = new RecordSet();
$rsRecordSeTTCMGOHML30 = new RecordSet();

$obTTCMGOHML = new TTCMGOHML();
$obTTCMGOHML->setDado('exercicio'   , Sessao::getExercicio());
$obTTCMGOHML->setDado('cod_entidade', $stEntidades);
$obTTCMGOHML->setDado('dt_inicial'  , $arFiltroRelatorio['stDataInicial'] );
$obTTCMGOHML->setDado('dt_final'    , $arFiltroRelatorio['stDataFinal']   );

$obTTCMGOHML->recupera10($rsRecordSetHML10,$boTransacao);
$obTTCMGOHML->recupera20($rsRecordSetHML20,$boTransacao);
$obTTCMGOHML->recupera30($rsRecordSetHML30,$boTransacao);

//Tipo Registro 99 - Declaro que no mês desta remessa não há informações inerentes ao arquivo “Homologação da Licitação.
$arRecordSetHML99 = array(
    '0' => array(
        'tipo_registro' => '99',
        'numero_registro' => '1'
    )
);

$rsRecordSetHML99 = new RecordSet();
$rsRecordSetHML99->preenche($arRecordSetHML99);

$inContador =0; 
$inCount=0;
$stChave30 = '';
$arRecordSetHML10 = $rsRecordSetHML10->getElementos();

if (count($arRecordSetHML10) > 0) {
    $stChave10 = '';
    foreach ( $arRecordSetHML10 as $arHML ) {
        $inContador++;
        if ( !($stChave10 === $arHML['tiporegistro']
                             .$arHML['cod_orgao']
                             .$arHML['cod_unidadesub']
                             .$arHML['exercicio_licitacao']
                             .$arHML['nro_processolicitatorio']
                             .$arHML['tipo_documento']
                             .$arHML['nro_documento']
                             .$arHML['nro_lote']
                             .$arHML['cod_item'])) 
        {
            $stChave10 =  $arHML['tiporegistro']
                         .$arHML['cod_orgao']
                         .$arHML['cod_unidadesub']
                         .$arHML['exercicio_licitacao']
                         .$arHML['nro_processolicitatorio']
                         .$arHML['tipo_documento']
                         .$arHML['nro_documento']
                         .$arHML['nro_lote']
                         .$arHML['cod_item'];

            //$stChaveCodReduzido = $arHML['cod_reduzido'];
            $stNumProcLic = $arHML['nro_processolicitatorio'];
            $arHML['numero_registro'] = ++$inCount;
            
            $rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arHML));
            
            $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidadesub");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processolicitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lote");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_item");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_unitario");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

            if (count($rsRecordSetHML20->getElementos()) > 0) {
               $stChave20 = '';

               //Percorre array de registros
               foreach ($rsRecordSetHML20->getElementos() as $arHML20) {
                    $stChave20Aux  = $arHML20['cod_orgao'].$arHML20['cod_unidadesub'].$arHML20['exercicio_licitacao'];
                    $stChave20Aux .= $arHML20['nro_processolicitatorio'].$arHML20['tipo_documento'].$arHML20['nro_documento'];
                    $stChave20Aux .= $arHML20['nro_lote'].$arHML20['cod_item'];                    

                    //Verifica se registro 20 bate com chave do registro 10
                    if ($stChave10 === '10'.$stChave20Aux) {
                        //Chave única do registro 20
                        if ($stChave20 !=  $arHML20['tiporegistro'].$stChave20Aux ) {
                            $stChave20 = $arHML20['tiporegistro'].$stChave20Aux;

                            $arHML20['numero_registro'] = ++$inCount;

                            $rsBloco20 = 'rsBloco20_'.$inCount;
                            unset($$rsBloco20);
                            $$rsBloco20 = new RecordSet();
                            $$rsBloco20->preenche(array($arHML20));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco20 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidadesub");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processolicitatorio");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lote");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("perc_desconto");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 265 );

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                        }
                    }
                }
            } // Fim do foreach principal HML20

            if($arRecordSetHMLC10[$inContador]['nro_processolicitatorio'] != $stNumProcLic){
                //Se houver registros no array
                if ( count($rsRecordSetHML30->getElementos()) > 0 ) {
                    //Percorre array de registros
                    foreach ($rsRecordSetHML30->getElementos() as $arHML30) {
                        $stChave30Aux = $arHML30['nro_processolicitatorio'];
                        //Verifica se registro 10 bate com chave do registro 30
                        if ( $stNumProcLic === $stChave30Aux ) {

                            $arHML30['numero_registro'] = ++$inCount;
                
                            $stChave30 = $stChave30Aux;
                            $rsBloco30 = 'rsBloco30_'.$inCount;
                            unset($$rsBloco30);
                            $$rsBloco30 = new RecordSet();
                            $$rsBloco30->preenche(array($arHML30));
                        
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco30 );
                   
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidadesub");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processolicitatorio");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_homologacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                   
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_adjudicacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(270);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                        }
                    }
                }
            } // Fim do foreach principal HML30
        } // Fim do foreach principal HML10
    }
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetHML99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(321);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador('');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
}

$rsRecordSetHOMML10 = null;
$obTTCEMGOHML       = null;
$rsRecordSetHML99   = null;
$arRecordSetHML10   = null;
?>