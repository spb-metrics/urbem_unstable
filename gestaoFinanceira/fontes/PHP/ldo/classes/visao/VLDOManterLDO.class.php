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
 * Classe Visao do 02.10.00 - Manter LDO
 * Data de Criação: 06/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 */

include_once CAM_GF_LDO_VISAO   . 'VLDOPadrao.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDOManterLDO.class.php';

class VLDOManterLDO extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Recupera a instância da classe
     * @return void
     */
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Inicia as Regras da Classe
     * @return void
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    public function recuperarLDO($stAnoLDO = null)
    {
        try {
            return RLDOManterLDO::recuperarInstancia()->recuperarLDO($stAnoLDO);
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'error', 'aviso', Sessao::getId(), '../');
        }
    }

    public function recuperarLDOHomologado($stAnoLDO = null)
    {
        try {
            return RLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado($stAnoLDO);
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'error', 'aviso', Sessao::getId(), '../');
        }
    }

    public function recuperarPPA($inCodPPA = null)
    {
        try {
            return RLDOManterLDO::recuperarInstancia()->recuperarPPA($inCodPPA);
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'error', 'error', Sessao::getId(), '../');
            exit();
        }
    }

    public function incluir(array $arParametros)
    {
    }

    public function alterar(array $arParametros)
    {
    }

    public function excluir(array $arParametros)
    {
    }
}
