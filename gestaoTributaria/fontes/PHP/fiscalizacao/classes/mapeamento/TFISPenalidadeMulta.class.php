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
 * Classe de mapeamento para FISCALIZACAO.PENALIDADE_MULTA
 * Data de Criação: 01/07/2008
 *
 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 *
 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISPenalidadeMulta.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Caso de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISPenalidadeMulta extends Persistente
{

    /**
     * Método construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela( 'fiscalizacao.penalidade_multa' );

        $this->setCampoCod( 'cod_penalidade' );
        $this->setComplementoChave( 'cod_penalidade,cod_indicador' );

        $this->addCampo( 'cod_penalidade', 'integer', true, '', true, true );
        $this->addCampo( 'cod_indicador', 'integer', true, '', false, true );
        $this->addCampo( 'cod_modulo', 'integer', true, '', false, true );
        $this->addCampo( 'cod_biblioteca', 'integer', true, '', false, true );
        $this->addCampo( 'cod_funcao', 'integer', true, '', false, true );
        $this->addCampo( 'cod_unidade', 'integer', true, '', false, true );
        $this->addCampo( 'cod_grandeza', 'integer', true, '', false, true );
    }

    public function recuperaPenalidadeMulta(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();

        $stSQL = $this->montaRecuperaPenalidadeMulta( $stCondicao ) . $stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

        return $obErro;
    }

    private function montaRecuperaPenalidadeMulta($stCondicao)
    {
        if ($stCondicao) {
            $stCondicao = " WHERE " . $stCondicao;
        }

        $stSQL  = "     SELECT fpm.cod_penalidade                                   \n";
        $stSQL .= "          , fpm.cod_indicador                                    \n";
        $stSQL .= "          , fpm.cod_modulo                                       \n";
        $stSQL .= "          , fpm.cod_biblioteca                                   \n";
        $stSQL .= "          , fpm.cod_funcao                                       \n";
        $stSQL .= "          , fpm.cod_unidade                                      \n";
        $stSQL .= "          , fpm.cod_grandeza                                     \n";
        $stSQL .= "          , mie.descricao                                        \n";
        $stSQL .= "       FROM fiscalizacao.penalidade_multa AS fpm                 \n";
        $stSQL .= " INNER JOIN monetario.indicador_economico AS mie                 \n";
        $stSQL .= "         ON mie.cod_indicador = fpm.cod_indicador                \n";
        $stSQL .= $stCondicao;
        //echo $stSQL;
        return $stSQL;
    }
}

?>
