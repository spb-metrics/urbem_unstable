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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CLA_ARQUIVO_ZIP                                                                         );
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioBeneficiario.class.php"                               );
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioBeneficiarioLancamento.class.php"                     );

class LayoutArquivoUnimed
{
    
    function RecuperaLayoutArquivoUnimed($arquivo)
    {
        $obArquivoZip    = new ArquivoZip;
        $obErro          = new Erro;
        $cont            = 0;
        $linha           = fgets($arquivo);
        $coluna          = explode(';', $linha);
        
        //Valida formato e padroes do arquivo
        $boArquivoValido = $this->validarArquivo($coluna);
        
        if ( $boArquivoValido ) {
            while (!feof($arquivo)) {
                $linha  = fgets($arquivo);
                $coluna = explode(';', $linha);
                //verifica se arquivo não está vazio e descarta primeira linha
                if (($cont > 0) && ($linha != '')) {
                    $stCondicao = " WHERE EXTRACT(YEAR FROM periodo_movimentacao.dt_final) = ".$coluna[0]."   \n";
                    $stCondicao.= "   AND EXTRACT(MONTH FROM periodo_movimentacao.dt_final) = ".$coluna[1]."; \n";
                
                    $obTBeneficioBeneficiarioLancamento = new TBeneficioBeneficiarioLancamento();
                    $obTBeneficioBeneficiarioLancamento->verificaPeriodoMovimentacao($rsPeriodo, $stCondicao);
                
                    if ($rsPeriodo->getNumLinhas() > 0) {
                        // Verifica se o usuário está cadastrado como beneficiário
                        $stCondicao = " WHERE cgm_fornecedor    = 1              \n";
                        $stCondicao.= "  AND codigo_usuario     = ".$coluna[4]." \n";
                        $stCondicao.= "  AND cod_modalidade     = ".$coluna[2]." \n";
                        $stCondicao.= "  AND cod_tipo_convenio  = ".$coluna[3]." \n";
                        $stCondicao.= "  AND timestamp_excluido is null \n";
                    
                        $obTBeneficioBeneficiario = new TBeneficioBeneficiario();
                        $obTBeneficioBeneficiario->recuperaTodos($rsBeneficioBeneficiario, $stCondicao);
                    
                        // Verifica se já foi feito lançamento para o usuário
                        $stCondicao = " WHERE codigo_usuario          = ".$coluna[4]."                                       \n";
                        $stCondicao.= "  AND cod_modalidade           = ".$coluna[2]."                                       \n";
                        $stCondicao.= "  AND cod_tipo_convenio        = ".$coluna[3]."                                       \n";
                        $stCondicao.= "  AND cod_periodo_movimentacao = ".$rsPeriodo->getCampo('cod_periodo_movimentacao')." \n";
                    
                        $obTBeneficioBeneficiarioLancamento = new TBeneficioBeneficiarioLancamento();
                        $obTBeneficioBeneficiarioLancamento->recuperaTodos($rsBeneficioBeneficiarioLancamento, $stCondicao);
                    
                        // Verifica se já foi feito lançamento para o usuário
                        if ($rsBeneficioBeneficiarioLancamento->getNumLinhas() < 1) {
                        
                            // Verifica se o usuário está cadastrado como beneficiário
                            if ($rsBeneficioBeneficiario->getNumLinhas() > 0) {
                                $obTBeneficioBeneficiarioLancamento = new TBeneficioBeneficiarioLancamento();
                                $obTBeneficioBeneficiarioLancamento->setDado('cod_contrato'             , $rsBeneficioBeneficiario->getCampo('cod_contrato'));
                                $obTBeneficioBeneficiarioLancamento->setDado('cgm_fornecedor'           , $rsBeneficioBeneficiario->getCampo('cgm_fornecedor'));
                                $obTBeneficioBeneficiarioLancamento->setDado('cod_modalidade'           , $rsBeneficioBeneficiario->getCampo('cod_modalidade'));
                                $obTBeneficioBeneficiarioLancamento->setDado('cod_tipo_convenio'        , $rsBeneficioBeneficiario->getCampo('cod_tipo_convenio'));
                                $obTBeneficioBeneficiarioLancamento->setDado('codigo_usuario'           , $rsBeneficioBeneficiario->getCampo('codigo_usuario'));
                                $obTBeneficioBeneficiarioLancamento->setDado('timestamp'                , $rsBeneficioBeneficiario->getCampo('timestamp'));
                                $obTBeneficioBeneficiarioLancamento->setDado('valor'                    , str_replace(',', '.', str_replace('.', '', $coluna[6])));
                                $obTBeneficioBeneficiarioLancamento->setDado('timestamp_lancamento'     , date('Y-m-d H:i:s'));
                                $obTBeneficioBeneficiarioLancamento->setDado('cod_periodo_movimentacao' , $rsPeriodo->getCampo('cod_periodo_movimentacao'));
                                $obTBeneficioBeneficiarioLancamento->inclusao();
                            } else {
                                $beneficiarioNaoCadastrado[] = $linha;
                            }
                        } else {
                            $beneficiarioCadastrado[] = $linha;
                        }
                    } else {
                        $obErro->setDescricao('A data dos dados do arquivo não coincide com o período de movimentação da folha, verifique as coluna ano/mês do arquivo.');
                    }
                }
                $cont++;
            }//WHILE
        }else{//else validacao arquivo
            $obErro->setDescricao('O arquivo está fora dos padrões do Layout Unimed.');
        }
        
        fclose($arquivo);
        
        if ( !$obErro->ocorreu() ) {
            // Escresce no arquivo os beneficiários que não puderam ser inseridos lançamentos
            if (count($beneficiarioNaoCadastrado) > 0) {
                $fp = fopen(CAM_FRAMEWORK.'tmp/beneficiariosNaoCadastrados.csv', 'w');
                foreach ($beneficiarioNaoCadastrado as $beneficiario) {
                    fwrite($fp, $beneficiario);
                }
                fclose($fp);
                
                $obArquivoZip->AdicionarArquivo(CAM_FRAMEWORK.'tmp/beneficiariosNaoCadastrados.csv', 'beneficiariosNaoCadastrados.csv');
            }
        
            // Escresce no arquivo os beneficiários que já estão cadastrados
            if (count($beneficiarioCadastrado) > 0) {
                $fp = fopen(CAM_FRAMEWORK."tmp/beneficiariosJaCadastrados.csv", "w");
                foreach ($beneficiarioCadastrado as $beneficiario) {
                    fwrite($fp, $beneficiario);
                }
                fclose($fp);
                
                $obArquivoZip->AdicionarArquivo(CAM_FRAMEWORK.'tmp/beneficiariosJaCadastrados.csv', 'beneficiariosJaCadastrados.csv');
            }
        
            $arquivoDownload = $obArquivoZip->Show();
            Sessao::write('arquivo_download',$arquivoDownload);    
        }else{
            $pgForm = CAM_GRH_BEN_INSTANCIAS."planoSaude/FMManterImportacaoMensal.php";
            SistemaLegado::alertaAviso($pgForm, urlencode( $obErro->getDescricao()),"n_erro","erro", Sessao::getId(), "../" );
        }
        
        return $obErro;
    }

    //Função para validar as colunas no padrão do Layout Unimed
    function validarArquivo($coluna)
    {   
        //Padrao para as colunas
        $arPadrao[0] = 'ano';
        $arPadrao[1] = 'mes';
        $arPadrao[2] = 'modalidade';
        $arPadrao[3] = 'termo';
        $arPadrao[4] = 'codusu';
        $arPadrao[5] = 'nomusu';
        $arPadrao[6] = 'valor';
        
        //Remove espacos em branco nas descricoes da colunas
        $coluna[0] = TRIM($coluna[0]);
        $coluna[1] = TRIM($coluna[1]);
        $coluna[2] = TRIM($coluna[2]);
        $coluna[3] = TRIM($coluna[3]);
        $coluna[4] = TRIM($coluna[4]);
        $coluna[5] = TRIM($coluna[5]);
        $coluna[6] = TRIM($coluna[6]);

        //Compara as colunas com o padrao do arquivo
        $arComparacao = array_diff($coluna, $arPadrao);

        //Se o array de comparação estiver vazio signfica que os arrays são iguais e o arquivo é válido
        if ( empty($arComparacao) )
            return true;
        else
            return false;
    }

}

?>